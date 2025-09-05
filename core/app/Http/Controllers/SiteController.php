<?php

namespace App\Http\Controllers;
use App\Models\AdminNotification;
use App\Models\Frontend;
use App\Models\Language;
use App\Models\Page;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Models\Forum;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Advertisement;
use App\Models\Reaction;
use App\Models\SupportAttachment;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class SiteController extends Controller
{
    public function __construct(){
        $this->activeTemplate = activeTemplate();
    }

    public function index(){
        $count = Page::where('tempname',$this->activeTemplate)->where('slug','home')->count();
        if($count == 0){
            $page = new Page();
            $page->tempname = $this->activeTemplate;
            $page->name = 'HOME';
            $page->slug = 'home';
            $page->save();
        }

        $reference = @$_GET['reference'];
        if ($reference) {
            session()->put('reference', $reference);
        }

        $forums = Forum::where('status', 1)->latest()->paginate(getPaginate(10));

        $pageTitle = 'Home';
        $sections = Page::where('tempname',$this->activeTemplate)->where('slug','home')->first();
        return view($this->activeTemplate . 'home', compact('pageTitle','sections', 'forums'));
    }

    public function pages($slug)
    {
        $page = Page::where('tempname',$this->activeTemplate)->where('slug',$slug)->firstOrFail();
        $pageTitle = $page->name;
        $sections = $page->secs;
        return view($this->activeTemplate . 'pages', compact('pageTitle','sections'));
    }


    public function contact()
    {
        $pageTitle = "Contact Us";
        return view($this->activeTemplate . 'contact',compact('pageTitle'));
    }


    public function contactSubmit(Request $request)
    {

        $attachments = $request->file('attachments');
        $allowedExts = array('jpg', 'png', 'jpeg', 'pdf');

        $this->validate($request, [
            'name' => 'required|max:191',
            'email' => 'required|max:191',
            'subject' => 'required|max:100',
            'message' => 'required',
        ]);


        $random = getNumber();

        $ticket = new SupportTicket();
        $ticket->user_id = auth()->id() ?? 0;
        $ticket->name = $request->name;
        $ticket->email = $request->email;
        $ticket->priority = 2;


        $ticket->ticket = $random;
        $ticket->subject = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status = 0;
        $ticket->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title = 'A new support ticket has opened ';
        $adminNotification->click_url = urlPath('admin.ticket.view',$ticket->id);
        $adminNotification->save();

        $message = new SupportMessage();
        $message->supportticket_id = $ticket->id;
        $message->message = $request->message;
        $message->save();

        $notify[] = ['success', 'ticket created successfully!'];

        return redirect()->route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function changeLanguage($lang = null)
    {
        $language = Language::where('code', $lang)->first();
        if (!$language) $lang = 'en';
        session()->put('lang', $lang);
        return redirect()->back();
    }

    public function blogDetails($id,$slug){
        $blog = Frontend::where('id',$id)->where('data_keys','blog.element')->firstOrFail();
        $pageTitle = $blog->data_values->title;
        return view($this->activeTemplate.'blog_details',compact('blog','pageTitle'));
    }


    public function cookieAccept(){
        session()->put('cookie_accepted',true);
        $notify[] = ['success','Cookie accepted successfully'];
        return back()->withNotify($notify);
    }

    public function placeholderImage($size = null){
        $imgWidth = explode('x',$size)[0];
        $imgHeight = explode('x',$size)[1];
        $text = $imgWidth . '×' . $imgHeight;
        $fontFile = realpath('assets/font') . DIRECTORY_SEPARATOR . 'RobotoMono-Regular.ttf';
        $fontSize = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if($imgHeight < 100 && $fontSize > 30){
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 175, 175, 175);
        imagefill($image, 0, 0, $bgFill);
        $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function policyPage($page, $id){
        $pageContent = Frontend::where('id',$id)->where('data_keys','policy_pages.element')->firstOrFail();
        $pageTitle = ucfirst($page);
        return view($this->activeTemplate.'policy_page',compact('pageContent','pageTitle'));
    }

    public function adRedirect($hash){

        $id = decrypt($hash);
        $ad = Advertisement::findOrFail($id);
        $ad->increment('click');
        $ad->save();

        return redirect($ad->url);
    }

    public function categoryPosts($slug, $id){

        $category = Category::where('id', $id)
                            ->where('status', 1)
                            ->whereHas('forum', function($q){
                                $q->where('status', 1);
                            })->firstOrFail();

        $subCats = $category->subCategory()->where('status', 1)->get(['id']);

        $posts = Post::where('status', 1)
                     ->whereIn('sub_category_id', $subCats)
                     ->with(['user', 'subCategory.category'])
                     ->latest()
                     ->paginate(getPaginate());

        $pageTitle = 'Tous les messages dans '.$category->name;
        return view($this->activeTemplate.'post',compact('pageTitle','posts'));
    }

    public function postDetails($slug, $id){

        $post = Post::where('id', $id)
                    ->with(['user'])
                    ->where('status', 1)
                    ->whereHas('subCategory', function($subCat){
                        $subCat->where('status', 1)->whereHas('category', function($cat){
                            $cat->where('status', 1)->whereHas('forum', function($forum){
                                $forum->where('status', 1);
                            });
                        });
                    })
                    ->firstOrFail();

        $post->increment('view');
        $post->save();

        $pageTitle = $post->post_title;
        $user = Auth::user();

        $comments = Comment::where('post_id', $post->id)->with('user')->latest()->take(5)->get();
              
        return view($this->activeTemplate.'post_details',compact('pageTitle', 'post', 'user', 'comments'));
    }

    public function moreComment(Request $request){

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:comments,id',
            'postId' => 'required|exists:posts,id'
        ]);

        if(!$validator->passes()) {
            return response()->json(['error'=>$validator->errors()->all()]);
        }

        $post = Post::where('id', $request->postId)->firstOrFail();

        if(!$post){
            return response()->json(['success'=>false, 'message'=>'Invalid Request']);
        }

        $id = $request->id - 4;

        $comments = Comment::where('id', '<', $id)
                               ->where('post_id', $request->postId)
                               ->with('user')
                               ->latest()
                               ->take(5)
                               ->get();

        $nextComment = Comment::where('id','<',@$comments[0]->id??1)->where('post_id', $request->postId)->first();

        if($nextComment){
            $msg = 200;
        }else{
            $msg = 400;
        }

        return response()->json(['success'=>true, 'array'=>$comments,'message'=>$msg]);
    }

    public function search(Request $request){

        $input = $request->title;

        if(!$input){
            $notify[] = ['error','Please Enter Post Title'];
            return back()->withNotify($notify);
        }

        $posts = Post::where('post_title', 'LIKE', '%' . $input . '%')
                     ->whereHas('subCategory', function($subCat){
                         $subCat->where('status', 1)->whereHas('category', function($cat){
                             $cat->where('status', 1)->whereHas('forum', function($forum){
                                 $forum->where('status', 1);
                             });
                         });
                     })
                     ->where('status', 1)
                     ->with(['user', 'subCategory'])
                     ->latest()
                     ->paginate(getPaginate());

        $pageTitle = $input;
        return view($this->activeTemplate.'post',compact('pageTitle','posts'));
    }

    public function allPost(){
        $posts = Post::whereHas('subCategory', function($subCat){
                        $subCat->where('status', 1)->whereHas('category', function($cat){
                            $cat->where('status', 1)->whereHas('forum', function($forum){
                                $forum->where('status', 1);
                            });
                        });
                     })
                     ->where('status', 1)
                     ->with(['user', 'subCategory'])
                     ->latest()
                     ->paginate(getPaginate());

        $pageTitle = 'All Posts';
        return view($this->activeTemplate.'post',compact('pageTitle','posts'));
    }

    public function forum($slub, $id){
        $forum = Forum::where('status', 1)->where('id', $id)->firstOrFail();
        $pageTitle = $forum->name;
        return view($this->activeTemplate.'forum',compact('pageTitle','forum'));
    }

    public function subCategoryPosts($slug, $id){

        $subCategory = SubCategory::where('id', $id)
                            ->where('status', 1)
                            ->whereHas('category', function($cat){
                                $cat->where('status', 1)->whereHas('forum', function($forum){
                                    $forum->where('status', 1);
                                });
                            })->select('id', 'name')->firstOrFail();

        $posts = Post::where('status', 1)
                     ->where('sub_category_id', $subCategory->id)
                     ->with(['user', 'subCategory.category'])
                     ->latest()
                     ->paginate(getPaginate());

        $pageTitle = 'Tous les messages dans '.$subCategory->name;
        return view($this->activeTemplate.'post',compact('pageTitle','posts'));
    }

    public function user($slug, $id){
        $user = User::findOrFail($id);
        $pageTitle = 'User '.$user->fullname;
        return view($this->activeTemplate.'profile',compact('pageTitle','user'));
    }

    public function userTopics($slug, $id){

        $user = User::findOrFail($id);
        $pageTitle = 'Topics of '.$user->fullname;

        $posts = Post::where('user_id', $id)
                     ->where('status', 1)
                     ->with('subCategory')
                     ->latest()
                     ->whereHas('subCategory', function($subCat){
                        $subCat->where('status', 1)->whereHas('category', function($cat){
                            $cat->where('status', 1)->whereHas('forum', function($forum){
                                $forum->where('status', 1);
                            });
                        });
                     })
                     ->paginate(getPaginate());

        return view($this->activeTemplate.'profile_post',compact('pageTitle','user', 'posts'));
    }

    public function userAnswer($slug, $id){

        $user = User::findOrFail($id);
        $pageTitle = 'Answered '.$user->fullname;

        $comments = Comment::where('user_id', $id)->get('post_id')->toArray();

        $posts = Post::where('status', 1)
                     ->whereIn('id', $comments)
                     ->latest()
                     ->whereHas('subCategory', function($subCat){
                        $subCat->where('status', 1)->whereHas('category', function($cat){
                            $cat->where('status', 1)->whereHas('forum', function($forum){
                                $forum->where('status', 1);
                            });
                        });
                     })
                     ->paginate(getPaginate());

        return view($this->activeTemplate.'profile_post',compact('pageTitle','user', 'posts'));
    }

    public function userUpVote($slug, $id){

        $user = User::findOrFail($id);
        $pageTitle = 'Up Vote '.$user->fullname;

        $upVotes = Reaction::where('user_id', $id)->where('reaction', 1)->get('post_id');

        $posts = Post::where('status', 1)
                     ->whereIn('id', $upVotes)
                     ->latest()
                     ->whereHas('subCategory', function($subCat){
                        $subCat->where('status', 1)->whereHas('category', function($cat){
                            $cat->where('status', 1)->whereHas('forum', function($forum){
                                $forum->where('status', 1);
                            });
                        });
                     })
                     ->paginate(getPaginate());

        return view($this->activeTemplate.'profile_post',compact('pageTitle','user', 'posts'));
    }

    public function userDownVote($slug, $id){

        $user = User::findOrFail($id);
        $pageTitle = 'Down Vote '.$user->fullname;

        $downVotes = Reaction::where('user_id', $id)->where('reaction', 0)->get('post_id');

        $posts = Post::where('status', 1)
                     ->whereIn('id', $downVotes)
                     ->latest()
                     ->whereHas('subCategory', function($subCat){
                        $subCat->where('status', 1)->whereHas('category', function($cat){
                            $cat->where('status', 1)->whereHas('forum', function($forum){
                                $forum->where('status', 1);
                            });
                        });
                     })
                     ->paginate(getPaginate());

        return view($this->activeTemplate.'profile_post',compact('pageTitle','user', 'posts'));
    }



    public function adCountAjax(Request $request){
        $hash = $request->id;
        $ad = Advertisement::where('id',decrypt($hash))->first();
        if ($ad) {
            $ad->click += 1;
            $ad->save();
        }
    }


}
