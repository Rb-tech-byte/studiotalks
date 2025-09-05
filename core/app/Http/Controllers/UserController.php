<?php

namespace App\Http\Controllers;

use App\Lib\GoogleAuthenticator;
use App\Models\AdminNotification;
use App\Models\GeneralSetting;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Transaction;
use App\Models\WithdrawMethod;
use App\Models\SupportTicket;
use App\Models\Reaction;
use App\Models\Withdrawal;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    public function home()
    {
        $pageTitle = 'Dashboard';
        $user = Auth::user();

        $posts = Post::where('user_id', $user->id)
                     ->where('status', '!=', 3)
                     ->latest()
                     ->limit(8)
                     ->whereHas('subCategory', function($subCat){
                        $subCat->where('status', 1)->whereHas('category', function($cat){
                            $cat->where('status', 1)->whereHas('forum', function($forum){
                                $forum->where('status', 1);
                            });
                        });
                     })
                     ->get();

        $countPost = Post::where('user_id', $user->id)->where('status', '!=', 3)->count();
        $countTicket = SupportTicket::where('user_id', $user->id)->count();

        return view($this->activeTemplate . 'user.dashboard', compact('pageTitle', 'posts', 'countPost', 'countTicket'));
    }

    public function profile()
    {
        $pageTitle = "Profile Setting";
        $user = Auth::user();
        return view($this->activeTemplate. 'user.profile_setting', compact('pageTitle','user'));
    }

    public function submitProfile(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'address' => 'sometimes|required|max:80',
            'state' => 'sometimes|required|max:80',
            'zip' => 'sometimes|required|max:40',
            'city' => 'sometimes|required|max:50',
            'about' => 'required|max:60000',
            'image' => ['image',new FileTypeValidate(['jpg','jpeg','png'])]
        ],[
            'firstname.required'=>'First name field is required',
            'lastname.required'=>'Last name field is required'
        ]);

        $user = Auth::user();

        $in['firstname'] = $request->firstname;
        $in['lastname'] = $request->lastname;
        $in['about'] = $request->about;

        $in['address'] = [
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => @$user->address->country,
            'city' => $request->city,
        ];


        if ($request->hasFile('image')) {
            $location = imagePath()['profile']['user']['path'];
            $size = imagePath()['profile']['user']['size'];
            $filename = uploadImage($request->image, $location, $size, $user->image);
            $in['image'] = $filename;
        }

        $user->fill($in)->save();
        $notify[] = ['success', 'Profile updated successfully.'];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $pageTitle = 'Change password';
        return view($this->activeTemplate . 'user.password', compact('pageTitle'));
    }

    public function submitPassword(Request $request)
    {

        $password_validation = Password::min(6);
        $general = GeneralSetting::first();
        if ($general->secure_password) {
            $password_validation = $password_validation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $this->validate($request, [
            'current_password' => 'required',
            'password' => ['required','confirmed',$password_validation]
        ]);


        try {
            $user = auth()->user();
            if (Hash::check($request->current_password, $user->password)) {
                $password = Hash::make($request->password);
                $user->password = $password;
                $user->save();
                $notify[] = ['success', 'Password changes successfully.'];
                return back()->withNotify($notify);
            } else {
                $notify[] = ['error', 'The password doesn\'t match!'];
                return back()->withNotify($notify);
            }
        } catch (\PDOException $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    /*
     * Deposit History
     */
    public function depositHistory()
    {
        $pageTitle = 'Deposit History';
        $emptyMessage = 'No history found.';
        $logs = auth()->user()->deposits()->with(['gateway'])->orderBy('id','desc')->paginate(getPaginate());
        return view($this->activeTemplate.'user.deposit_history', compact('pageTitle', 'emptyMessage', 'logs'));
    }

    /*
     * Withdraw Operation
     */

    public function withdrawMoney()
    {
        $withdrawMethod = WithdrawMethod::where('status',1)->get();
        $pageTitle = 'Withdraw Money';
        return view($this->activeTemplate.'user.withdraw.methods', compact('pageTitle','withdrawMethod'));
    }

    public function withdrawStore(Request $request)
    {
        $this->validate($request, [
            'method_code' => 'required',
            'amount' => 'required|numeric'
        ]);
        $method = WithdrawMethod::where('id', $request->method_code)->where('status', 1)->firstOrFail();
        $user = auth()->user();
        if ($request->amount < $method->min_limit) {
            $notify[] = ['error', 'Your requested amount is smaller than minimum amount.'];
            return back()->withNotify($notify);
        }
        if ($request->amount > $method->max_limit) {
            $notify[] = ['error', 'Your requested amount is larger than maximum amount.'];
            return back()->withNotify($notify);
        }

        if ($request->amount > $user->balance) {
            $notify[] = ['error', 'You do not have sufficient balance for withdraw.'];
            return back()->withNotify($notify);
        }


        $charge = $method->fixed_charge + ($request->amount * $method->percent_charge / 100);
        $afterCharge = $request->amount - $charge;
        $finalAmount = $afterCharge * $method->rate;

        $withdraw = new Withdrawal();
        $withdraw->method_id = $method->id; // wallet method ID
        $withdraw->user_id = $user->id;
        $withdraw->amount = $request->amount;
        $withdraw->currency = $method->currency;
        $withdraw->rate = $method->rate;
        $withdraw->charge = $charge;
        $withdraw->final_amount = $finalAmount;
        $withdraw->after_charge = $afterCharge;
        $withdraw->trx = getTrx();
        $withdraw->save();
        session()->put('wtrx', $withdraw->trx);
        return redirect()->route('user.withdraw.preview');
    }

    public function withdrawPreview()
    {
        $withdraw = Withdrawal::with('method','user')->where('trx', session()->get('wtrx'))->where('status', 0)->orderBy('id','desc')->firstOrFail();
        $pageTitle = 'Withdraw Preview';
        return view($this->activeTemplate . 'user.withdraw.preview', compact('pageTitle','withdraw'));
    }


    public function withdrawSubmit(Request $request)
    {
        $general = GeneralSetting::first();
        $withdraw = Withdrawal::with('method','user')->where('trx', session()->get('wtrx'))->where('status', 0)->orderBy('id','desc')->firstOrFail();

        $rules = [];
        $inputField = [];
        if ($withdraw->method->user_data != null) {
            foreach ($withdraw->method->user_data as $key => $cus) {
                $rules[$key] = [$cus->validation];
                if ($cus->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], new FileTypeValidate(['jpg','jpeg','png']));
                    array_push($rules[$key], 'max:2048');
                }
                if ($cus->type == 'text') {
                    array_push($rules[$key], 'max:191');
                }
                if ($cus->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }
                $inputField[] = $key;
            }
        }

        $this->validate($request, $rules);

        $user = auth()->user();
        if ($user->ts) {
            $response = verifyG2fa($user,$request->authenticator_code);
            if (!$response) {
                $notify[] = ['error', 'Wrong verification code'];
                return back()->withNotify($notify);
            }
        }


        if ($withdraw->amount > $user->balance) {
            $notify[] = ['error', 'Your request amount is larger then your current balance.'];
            return back()->withNotify($notify);
        }

        $directory = date("Y")."/".date("m")."/".date("d");
        $path = imagePath()['verify']['withdraw']['path'].'/'.$directory;
        $collection = collect($request);
        $reqField = [];
        if ($withdraw->method->user_data != null) {
            foreach ($collection as $k => $v) {
                foreach ($withdraw->method->user_data as $inKey => $inVal) {
                    if ($k != $inKey) {
                        continue;
                    } else {
                        if ($inVal->type == 'file') {
                            if ($request->hasFile($inKey)) {
                                try {
                                    $reqField[$inKey] = [
                                        'field_name' => $directory.'/'.uploadImage($request[$inKey], $path),
                                        'type' => $inVal->type,
                                    ];
                                } catch (\Exception $exp) {
                                    $notify[] = ['error', 'Could not upload your ' . $request[$inKey]];
                                    return back()->withNotify($notify)->withInput();
                                }
                            }
                        } else {
                            $reqField[$inKey] = $v;
                            $reqField[$inKey] = [
                                'field_name' => $v,
                                'type' => $inVal->type,
                            ];
                        }
                    }
                }
            }
            $withdraw['withdraw_information'] = $reqField;
        } else {
            $withdraw['withdraw_information'] = null;
        }

        $withdraw->status = 2;
        $withdraw->save();
        $user->balance  -=  $withdraw->amount;
        $user->save();

        $transaction = new Transaction();
        $transaction->user_id = $withdraw->user_id;
        $transaction->amount = $withdraw->amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge = $withdraw->charge;
        $transaction->trx_type = '-';
        $transaction->details = showAmount($withdraw->final_amount) . ' ' . $withdraw->currency . ' Withdraw Via ' . $withdraw->method->name;
        $transaction->trx =  $withdraw->trx;
        $transaction->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New withdraw request from '.$user->username;
        $adminNotification->click_url = urlPath('admin.withdraw.details',$withdraw->id);
        $adminNotification->save();

        notify($user, 'WITHDRAW_REQUEST', [
            'method_name' => $withdraw->method->name,
            'method_currency' => $withdraw->currency,
            'method_amount' => showAmount($withdraw->final_amount),
            'amount' => showAmount($withdraw->amount),
            'charge' => showAmount($withdraw->charge),
            'currency' => $general->cur_text,
            'rate' => showAmount($withdraw->rate),
            'trx' => $withdraw->trx,
            'post_balance' => showAmount($user->balance),
            'delay' => $withdraw->method->delay
        ]);

        $notify[] = ['success', 'Withdraw request sent successfully'];
        return redirect()->route('user.withdraw.history')->withNotify($notify);
    }

    public function withdrawLog()
    {
        $pageTitle = "Withdraw Log";
        $withdraws = Withdrawal::where('user_id', Auth::id())->where('status', '!=', 0)->with('method')->orderBy('id','desc')->paginate(getPaginate());
        $data['emptyMessage'] = "No Data Found!";
        return view($this->activeTemplate.'user.withdraw.log', compact('pageTitle','withdraws'));
    }



    public function show2faForm()
    {
        $general = GeneralSetting::first();
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $general->sitename, $secret);
        $pageTitle = 'Two Factor';
        return view($this->activeTemplate.'user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user,$request->code,$request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts = 1;
            $user->save();
            $userAgent = getIpInfo();
            $osBrowser = osBrowser();
            notify($user, '2FA_ENABLE', [
                'operating_system' => @$osBrowser['os_platform'],
                'browser' => @$osBrowser['browser'],
                'ip' => @$userAgent['ip'],
                'time' => @$userAgent['time']
            ]);
            $notify[] = ['success', 'Google authenticator enabled successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }


    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user = auth()->user();
        $response = verifyG2fa($user,$request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts = 0;
            $user->save();
            $userAgent = getIpInfo();
            $osBrowser = osBrowser();
            notify($user, '2FA_DISABLE', [
                'operating_system' => @$osBrowser['os_platform'],
                'browser' => @$osBrowser['browser'],
                'ip' => @$userAgent['ip'],
                'time' => @$userAgent['time']
            ]);
            $notify[] = ['success', 'Two factor authenticator disable successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function postForm(){
        $pageTitle = 'Create New Topics';
        $subCategories = SubCategory::where('status', 1)
                                ->latest()
                                ->whereHas('category', function($cat){
                                    $cat->where('status', 1)->whereHas('forum', function($forum){
                                        $forum->where('status', 1);
                                    });
                                })
                                ->get();
        return view($this->activeTemplate.'user.post.form', compact('pageTitle', 'subCategories'));
    }

    public function postCreate(Request $request){

        $request->validate([
            'sub_category' => 'required|exists:sub_categories,id',
            'title' => 'required|string|max:191',
            'image' => ['nullable','image',new FileTypeValidate(['jpg','jpeg','png'])],
            'des' => 'required|string|max:64000',
            'video' => 'nullable|url|string|max:64000',
            'tags' => 'required|array|max:60000'
        ]);

        SubCategory::where('status', 1)
                   ->where('id', $request->sub_category)
                   ->whereHas('category', function($cat){
                       $cat->where('status', 1)->whereHas('forum', function($forum){
                           $forum->where('status', 1);
                       });
                   })
                ->firstOrFail();

        $general = GeneralSetting::first();
        $approve = $general->auto_approve ? 1 : 2;

        $user = Auth::user();

        $image = null;
        if ($request->hasFile('image')) {
            $image = uploadImage($request->image,imagePath()['post']['path'],imagePath()['post']['size']);
        }

        $new = new Post();
        $new->user_id = $user->id;
        $new->tags = json_encode($request->tags);
        $new->sub_category_id = $request->sub_category;
        $new->post_title = $request->title;
        $new->description = $request->des;
        $new->status = $approve;
        $new->image = $image;
        $new->video = $request->video;
        $new->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'Created post from '.$user->username;
        $adminNotification->click_url = urlPath('admin.users.post.all',$user->id);
        $adminNotification->save();

        $notify[] = ['success', 'Post created successfully.'];
        return redirect()->route('user.post.all')->withNotify($notify);
    }

    public function updatePostForm($id){

        $pageTitle = 'Update Topic';
        $user = Auth::user();

        $post = Post::where('user_id', $user->id)
                    ->where('id', $id)
                    ->where('status', '!=', 3)
                    ->whereHas('subCategory', function($subCat){
                        $subCat->where('status', 1)->whereHas('category', function($cat){
                            $cat->where('status', 1)->whereHas('forum', function($forum){
                                $forum->where('status', 1);
                            });
                        });
                  })->firstOrFail();

        $subCategories = SubCategory::where('status', 1)
                                    ->whereHas('category', function($cat){
                                        $cat->where('status', 1)->whereHas('forum', function($forum){
                                            $forum->where('status', 1);
                                        });
                                    })
                                ->get();

        return view($this->activeTemplate.'user.post.update', compact('pageTitle', 'subCategories', 'post'));
    }

    public function updatePost(Request $request){

        $request->validate([
            'id' => 'required|exists:posts,id',
            'sub_category' => 'required|exists:sub_categories,id',
            'title' => 'required|string|max:191',
            'des' => 'required|string|max:64000',
            'video' => 'nullable|url|string|max:64000',
            'image' => ['nullable','image',new FileTypeValidate(['jpg','jpeg','png'])],
            'tags' => 'required|array|max:60000'
        ]);

        $post = Post::where('user_id', Auth::user()->id)
                    ->where('id', $request->id)
                    ->where('status', '!=', 3)
                    ->whereHas('subCategory', function($subCat){
                        $subCat->where('status', 1)->whereHas('category', function($cat){
                            $cat->where('status', 1)->whereHas('forum', function($forum){
                                $forum->where('status', 1);
                            });
                        });
                    })->firstOrFail();

        $image = $post->image;
        if ($request->hasFile('image')) {
            $image = uploadImage($request->image,imagePath()['post']['path'],imagePath()['post']['size'],$post->image);
        }

        $post->user_id = Auth::user()->id;
        $post->tags = json_encode($request->tags);
        $post->sub_category_id = $request->sub_category;
        $post->post_title = $request->title;
        $post->description = $request->des;
        $post->image = $image;
        $post->video = $request->video;
        $post->save();

        $notify[] = ['success', 'Post updated successfully.'];
        return back()->withNotify($notify);
    }

    public function deletePost(Request $request){

        $request->validate([
            'id' => 'required|exists:posts,id',
        ]);

        $post = Post::where('id', $request->id)
                    ->where('user_id', Auth::user()->id)
                    ->where('status', '!=', 3)
                    ->whereHas('subCategory', function($subCat){
                        $subCat->where('status', 1)->whereHas('category', function($cat){
                            $cat->where('status', 1)->whereHas('forum', function($forum){
                                $forum->where('status', 1);
                            });
                        });
                    })
                    ->firstOrFail();

        $post->status = 3;
        $post->save();

        $notify[] = ['success', 'Post deleted successfully.'];
        return back()->withNotify($notify);
    }

    public function posts(){
        $pageTitle = 'All Topics';

        $posts = Post::where('user_id', Auth::user()->id)
                     ->where('status', '!=', 3)
                     ->latest()
                     ->whereHas('subCategory', function($subCat){
                        $subCat->where('status', 1)->whereHas('category', function($cat){
                            $cat->where('status', 1)->whereHas('forum', function($forum){
                                $forum->where('status', 1);
                            });
                        });
                     })
                     ->paginate(getPaginate());

        return view($this->activeTemplate.'user.post.index', compact('pageTitle', 'posts'));
    }

    public function reaction(Request $request){

        $validator = Validator::make($request->all(), [
            'value' => 'required|in:0,1',
            'id' => 'required|exists:posts,id'
        ]);

        if(!$validator->passes()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $post = Post::where('id', $request->id)
                    ->where('status', 1)
                    ->whereHas('subCategory', function($subCat){
                        $subCat->where('status', 1)->whereHas('category', function($cat){
                            $cat->where('status', 1)->whereHas('forum', function($forum){
                                $forum->where('status', 1);
                            });
                        });
                    })
                    ->first();

        if(!$post){
            return response()->json(['success'=>false, 'message'=>'Invalid Request']);
        }

        $user = Auth::user();
        $reaction = Reaction::where('user_id', $user->id)->where('post_id', $post->id)->first();

        $userReact = $request->value;

        if($reaction){
            if($reaction->reaction == $userReact){
                $message = $userReact == 1 ? 'Already Up Voted' : 'Already Down Voted';
                return response()->json(['success'=>false, 'message'=>$message]);
            }else{
                if($userReact == 1){
                    $reaction->reaction = 1;
                    $reaction->save();

                    $post->increment('up_vote');
                    $post->decrement('down_vote');
                    $post->save();

                    return response()->json([
                        'success'=>true,
                        'message'=>'Added Up Vote Successfully',
                        'down'=>$post->down_vote,
                        'up'=>$post->up_vote
                    ]);
                }else{
                    $reaction->reaction = 0;
                    $reaction->save();

                    $post->decrement('up_vote');
                    $post->increment('down_vote');
                    $post->save();

                    return response()->json([
                        'success'=>true,
                        'message'=>'Added Down Vote Successfully',
                        'down'=>$post->down_vote,
                        'up'=>$post->up_vote
                    ]);
                }
            }
        }

        $newReact = new Reaction();
        $newReact->user_id = $user->id;
        $newReact->post_id = $post->id;
        $newReact->reaction = $userReact;
        $newReact->save();

        $react = null;

        if($newReact->reaction == 1){
            $react = 'Added Up Vote Successfully';
            $post->increment('up_vote');
            $post->save();
        }else{
            $react = 'Added Down Vote Success';
            $post->increment('down_vote');
            $post->save();
        }

        return response()->json([
            'success'=>true,
            'message'=>$react,
            'down'=>$post->down_vote,
            'up'=>$post->up_vote
        ]);
    }

    public function comment(Request $request){

        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:60000',
            'id' => 'required|exists:posts,id'
        ]);

        if(!$validator->passes()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $post = Post::where('id', $request->id)
                    ->where('status', 1)
                    ->whereHas('subCategory', function($subCat){
                        $subCat->where('status', 1)->whereHas('category', function($cat){
                            $cat->where('status', 1)->whereHas('forum', function($forum){
                                $forum->where('status', 1);
                            });
                        });
                    })
                    ->first();

        if(!$post){
            return response()->json(['success'=>false, 'message'=>'Invalid Request']);
        }

        $user = Auth::user();

        $comment = new Comment();
        $comment->user_id = $user->id;
        $comment->post_id = $post->id;
        $comment->comment = $request->comment;
        $comment->save();

        $post->increment('comment');
        $post->save();

        return response()->json([
            'success'=>true,
            'message'=>'Comment Created Successfully',
            'count'=>$post->comment,
            'user'=>$user->fullname,
            'userId'=>$user->id,
            'username'=>$user->username,
            'image'=>getImage(
                imagePath()['profile']['user']['path'].'/'.
                @$user->image,imagePath()['profile']['user']['size']
            ),
            'created'=>$comment->created_at->diffforhumans(),
            'comment'=>$comment->comment,
        ]);

    }



}
