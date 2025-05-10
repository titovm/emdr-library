<?php

namespace App\Http\Controllers;

use App\Models\VisitorStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    /**
     * Display visitor statistics dashboard.
     */
    public function index()
    {
        // Get total number of visitors
        $totalVisitors = VisitorStat::count();
        
        // Get unique visitors count
        $uniqueVisitors = VisitorStat::distinct('email')->count('email');
        
        // Get visitors per day for the last 30 days
        $visitorsPerDay = VisitorStat::select(
            DB::raw('DATE(visited_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('visited_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Get top visitors
        $topVisitors = VisitorStat::select('name', 'email', DB::raw('COUNT(*) as visits'))
            ->groupBy('name', 'email')
            ->orderByDesc('visits')
            ->limit(10)
            ->get();
        
        // Access methods breakdown
        $accessMethods = VisitorStat::select('access_method', DB::raw('COUNT(*) as count'))
            ->groupBy('access_method')
            ->orderByDesc('count')
            ->get();
        
        // Recent visitors with formatted page names
        $recentVisitors = VisitorStat::latest('visited_at')
            ->limit(10)
            ->get()
            ->map(function ($visitor) {
                $visitor->formatted_page = $this->formatPageName($visitor->page_visited);
                return $visitor;
            });
        
        // Most popular pages
        $popularPages = VisitorStat::select('page_visited', DB::raw('COUNT(*) as visits'))
            ->groupBy('page_visited')
            ->orderByDesc('visits')
            ->limit(10)
            ->get()
            ->map(function ($page) {
                $page->formatted_page = $this->formatPageName($page->page_visited);
                return $page;
            });
        
        // Chart data for JS
        $chartData = [
            'dates' => $visitorsPerDay->pluck('date'),
            'counts' => $visitorsPerDay->pluck('count'),
        ];
        
        return view('admin.stats', compact(
            'totalVisitors',
            'uniqueVisitors',
            'visitorsPerDay',
            'topVisitors',
            'accessMethods',
            'recentVisitors',
            'popularPages',
            'chartData'
        ));
    }
    
    /**
     * Format the page name to be more readable.
     */
    private function formatPageName($pageName)
    {
        if (empty($pageName)) {
            return 'Unknown Page';
        }
        
        // Handle specific route names
        if ($pageName === 'library.index') {
            return 'Library Home';
        }
        
        // Handle category pages
        if (preg_match('/^library\.category\.(.+)$/', $pageName, $matches)) {
            return 'Category: ' . ucfirst($matches[1]);
        }
        
        // Handle tag pages
        if (preg_match('/^library\.tag\.(.+)$/', $pageName, $matches)) {
            return 'Tag: ' . ucfirst($matches[1]);
        }
        
        // Handle library item pages
        if (preg_match('/^library\.show\.(\d+)$/', $pageName, $matches)) {
            return 'Item Details #' . $matches[1];
        }
        
        // Handle downloads
        if (preg_match('/^library\.download\.(\d+)$/', $pageName, $matches)) {
            return 'File Download #' . $matches[1];
        }
        
        // Default transformation for other pages
        return ucwords(str_replace(['.', '_'], [' - ', ' '], $pageName));
    }
}
