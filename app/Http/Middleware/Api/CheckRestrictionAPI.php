<?php

namespace App\Http\Middleware\Api;

use App\Models\IpRestriction;
use Closure;
use Illuminate\Http\Request;
use Torann\GeoIP\Facades\GeoIP;

class CheckRestrictionAPI
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $block = false;
        $userIp = $request->ip();

        $restrictions = IpRestriction::query()->get();

        foreach ($restrictions as $restriction) {
            $block = $this->checkIpRestriction($restriction, $userIp);
        }

        if ($block) {
            $restrictionSettings = getRestrictionSettings();
            return apiResponse2(false,'restriction', trans('api.public.retrieved'),[
                "title" => $restrictionSettings["title"] ,
                "image" => url($restrictionSettings["image"] ),
                "description" => $restrictionSettings["description"] ,
            ]);
        }

        return $next($request);
    }

    private function checkIpRestriction($restriction, $ip): bool
    {
        $block = false;

        if ($restriction->type == "country") {
            try {
                $location = GeoIP::getLocation($ip);

                if (!empty($location)) {
                    $userCountryCode = $location['iso_code'] ?? null;

                    $block = !!($restriction->value == $userCountryCode);
                }
            } catch (\Exception $exception) {

            }
        } elseif ($restriction->type == "full_ip") {
            $block = !!($restriction->value == $ip);
        } elseif ($restriction->type == "ip_range") {
            $block = $this->checkIpRange($ip, $restriction->value);
        }

        return $block;
    }

    private function checkIpRange($ip, $ipRange)
    {
        $ipParts = explode('.', $ip);
        $ipRangeParts = explode('.', $ipRange);

        for ($i = 0; $i < count($ipParts); $i++) {
            if ($ipRangeParts[$i] != '*' and $ipParts[$i] != $ipRangeParts[$i]) {
                return false;
            }
        }

        return true;
    }
}
