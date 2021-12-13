<?php

namespace App\Http\Middleware;

use App\Packages\Profiles\ProfileServiceInterface;
use Closure;
use Illuminate\Http\Request;

class UserProfileResolverMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->setRouteParamsWithRightOrder($request);
        return $next($request);
    }

    /**
     * Set the route params with right order by inserting the $profileId
     * and reinsert old params after $profileId.
     */
    private function setRouteParamsWithRightOrder(Request $request): void
    {
        $parameters = $request->route()->parameters();
        $request->route()->setParameter('profileId', $this->getProfileId());
        foreach ($parameters as $name => $value) {
            $request->route()->forgetParameter($name);
            $request->route()->setParameter($name, $value);
        }
    }

    /**
     * Return profile id for logged in user.
     */
    private function getProfileId(): string
    {
        $profileService = app()->make(ProfileServiceInterface::class);
        $ownerArn       = $this->getOwnerArn();
        $profile        = $profileService->getProfile(['ownerArn' => $ownerArn]);

        return $profile->getId();
    }

    /**
     * Return ownerArn for logged in user.
     */
    private function getOwnerArn(): string
    {
        return env('API_ARN_PREFIX', 'arn:local:api') . ':'.auth()->id();
    }
}
