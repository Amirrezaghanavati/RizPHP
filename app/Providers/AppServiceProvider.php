<?php

namespace App\Providers;

use System\View\Composer;

class AppServiceProvider extends provider
{
    public function boot(): void
    {
        Composer::view("app.index", function (){
            $ads = Ads::all();
            $sumArea = 0;
            foreach ($ads as $advertise)
            {
                $sumArea += (int) $advertise->area;
            }
            $usersCount = count(User::all());
            $postsCount = count(Post::all());
            return [
                "sumArea"       => $sumArea,
                "usersCount"    => $usersCount,
                "adsCount"      => count($ads),
                "postsCount"    => $postsCount
            ];
        });

    }
}