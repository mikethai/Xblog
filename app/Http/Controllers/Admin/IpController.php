<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Repositories\IpRepository;
use App\Http\Requests;
use App\Ip;
use Gate;

class IPController extends Controller
{
    protected $ipRepository;

    public function __construct(IpRepository $ipRepository)
    {
        $this->ipRepository = $ipRepository;
    }

    public function block($ip)
    {
        if ($this->ipRepository->toggleBlock($ip)) {
            return back()->with('success', "Action $ip successfully.");
        }
        return back()->withErrors("Blocked $ip failed.");
    }

    public function destroy($ip)
    {
        $ip = Ip::findOrFail($ip);
        if ($ip->blocked) {
            return back()->withErrors("UnBlocked $ip->id firstly.");
        }

        if (($count = $ip->comments()->withTrashed()->count()) > 0) {
            return back()->withErrors("$ip->id has $count comments.Please remove theme first.");
        }
        if ($ip->delete())
            return back()->with('success', "Delete $ip->id successfully.");
        return back()->withErrors("Blocked $ip->id failed.");
    }
}
