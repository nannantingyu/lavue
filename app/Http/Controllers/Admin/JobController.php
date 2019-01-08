<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Support\TemplateUpdater;

/**
 * 执行任务
 * Class JobController
 * @package App\Http\Controllers
 */
class JobController extends Controller
{
    public function __construct(TemplateUpdater $updater)
    {
        $this->updater = $updater;
    }

    /**
     * 手动生成静态页
     *
     * @auth job_update
     * @param Request $request
     * @return array
     */
    public function genetateTemplate(Request $request) {
        $url = $request->input('url');
        $type = $request->input('type');

        if(!is_null($url) && in_array($type, ['update', 'delete', 'api'])) {
            $this->updater->update($url, $type);
            return [ "success"=> 1, 'data'=>$this->updater ];
        }

        return [ "success"=> 0 ];
    }
}