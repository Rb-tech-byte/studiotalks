<?php

namespace App\Providers;

use App\Models\AdminNotification;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Models\Language;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Page;
use App\Models\Post;
use App\Models\Comment;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\Forum;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //$this->app['request']->server->set('HTTPS', false);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $activeTemplate = activeTemplate();

        // Safely fetch settings; skip if tables are not ready
        $general = null;
        if (Schema::hasTable('general_settings')) {
            $general = GeneralSetting::first();
        }

        $viewShare['general'] = $general;
        $viewShare['activeTemplate'] = $activeTemplate;
        $viewShare['activeTemplateTrue'] = activeTemplate(true);
        $viewShare['language'] = Schema::hasTable('languages') ? Language::all() : collect();
        $viewShare['pages'] = Schema::hasTable('pages')
            ? Page::where('tempname',$activeTemplate)->where('slug','!=','home')->get()
            : collect();
        view()->share($viewShare);


        view()->composer('admin.partials.sidenav', function ($view) {
            try {
                $view->with([
                    'banned_users_count'           => User::banned()->count(),
                    'email_unverified_users_count' => User::emailUnverified()->count(),
                    'sms_unverified_users_count'   => User::smsUnverified()->count(),
                    'pending_ticket_count'         => SupportTicket::whereIN('status', [0,2])->count(),
                    'pending_post'                 => Post::where('status', 2)->count(),
                ]);
            } catch (\Throwable $e) {
                $view->with([
                    'banned_users_count'           => 0,
                    'email_unverified_users_count' => 0,
                    'sms_unverified_users_count'   => 0,
                    'pending_ticket_count'         => 0,
                    'pending_post'                 => 0,
                ]);
            }
        });

        view()->composer('admin.partials.topnav', function ($view) {
            try {
                $view->with([
                    'adminNotifications'=>AdminNotification::where('read_status',0)->with('user')->orderBy('id','desc')->get(),
                ]);
            } catch (\Throwable $e) {
                $view->with([
                    'adminNotifications'=>collect(),
                ]);
            }
        });

        view()->composer($activeTemplate.'partials.left_bar', function ($view) {
            try {
                $view->with([
                    'forums' => Forum::where('status', 1)->latest()->get(),
                    'categories' => Category::where('status', 1)
                                            ->latest()
                                            ->with('subCategory')
                                            ->whereHas('forum', function($forum){
                                                $forum->where('status', 1);
                                            })
                                            ->get(),
                    'disscussions' => Post::where('status', 1)
                                          ->orderBy('comment', 'DESC')
                                          ->limit(10)
                                          ->whereHas('subCategory', function($subCat){
                                              $subCat->where('status', 1)->whereHas('category', function($cat){
                                                  $cat->where('status', 1)->whereHas('forum', function($forum){
                                                      $forum->where('status', 1);
                                                  });
                                              });
                                          })
                                          ->get(),
                ]);
            } catch (\Throwable $e) {
                $view->with([
                    'forums' => collect(),
                    'categories' => collect(),
                    'disscussions' => collect(),
                ]);
            }
        });

        view()->composer($activeTemplate.'partials.right_bar', function ($view) {
            try {
                $view->with([
                    'forum' => Forum::where('status', 1)->count(),
                    'post' => Post::where('status', 1)
                                  ->whereHas('subCategory', function($subCat){
                                      $subCat->where('status', 1)->whereHas('category', function($cat){
                                          $cat->where('status', 1)->whereHas('forum', function($forum){
                                              $forum->where('status', 1);
                                          });
                                      });
                                  })
                                  ->count(),
                    'category' => Category::where('status', 1)
                                          ->whereHas('forum', function($forum){
                                              $forum->where('status', 1);
                                          })
                                          ->count(),
                    'subCategory' => SubCategory::where('status', 1)
                                                ->whereHas('category', function($cat){
                                                    $cat->where('status', 1)->whereHas('forum', function($forum){
                                                        $forum->where('status', 1);
                                                    });
                                                })
                                                ->count(),
                    'unTalks' => Post::where('status', 1)
                                     ->where('comment',  0)
                                     ->with('user')
                                     ->latest()
                                     ->limit(10)
                                     ->whereHas('subCategory', function($subCat){
                                         $subCat->where('status', 1)->whereHas('category', function($cat){
                                             $cat->where('status', 1)->whereHas('forum', function($forum){
                                                 $forum->where('status', 1);
                                             });
                                         });
                                     })
                                     ->get(),
                    'topContributors' => Comment::selectRaw('user_id, count(*) as total')
                                                ->with('user')
                                                ->groupBy('user_id')
                                                ->orderBy('total', 'DESC')
                                                ->limit(10)
                                                ->get(),
                    'hots' => Comment::selectRaw('post_id, count(*) as total')
                                                ->whereDate('created_at', '>', Carbon::now()->subDays(3))
                                                ->with(['post.user'])
                                                ->groupBy('post_id')
                                                ->orderBy('total', 'DESC')
                                                ->limit(10)
                                                ->whereHas('post', function($post){
                                                    $post->where('status', 1)->whereHas('subCategory', function($subCat){
                                                        $subCat->where('status', 1)->whereHas('category', function($cat){
                                                            $cat->where('status', 1)->whereHas('forum', function($forum){
                                                                $forum->where('status', 1);
                                                            });
                                                        });
                                                    });
                                                })
                                                ->get(),
                ]);
            } catch (\Throwable $e) {
                $view->with([
                    'forum' => 0,
                    'post' => 0,
                    'category' => 0,
                    'subCategory' => 0,
                    'unTalks' => collect(),
                    'topContributors' => collect(),
                    'hots' => collect(),
                ]);
            }
        });

        view()->composer('partials.seo', function ($view) {
            $seo = Frontend::where('data_keys', 'seo.data')->first();
            $view->with([
                'seo' => $seo ? $seo->data_values : $seo,
            ]);
        });

        if($general && $general->force_ssl){
            \URL::forceScheme('http');
        }


        Paginator::useBootstrap();

    }
}
