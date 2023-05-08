<?php

class TaskRunner {

    static public function runAll() {

        $task_list = TaskRunner::getAllTasks();

        foreach ($task_list as $item) {

            if ($item->type == 0) {

            }
            else if ($item->type == 1) {

            }
            else if ($item->type == 2) {

            }
        }
    }

    static public function run($names,$seconds = 1) {

        TaskRunner::runWithFile( APP_ROOT . "Tasks/".$names.".php");

        // $task_list = TaskRunner::getAllTasks();

        // $names = explode(',',$names);

        // foreach ($task_list as $item) {

        //     if (in_array($item->name,$names)) {



        //         // if ($item->type == 0) {
                    
        //         //     while 
        //         // }
        //         // else if ($item->type == 1) {
    
        //         // }
        //         // else if ($item->type == 2) {
    
        //         // }
        //     }
        // }
    }

    static public function runWithFile($filePath,$paramString = '') {

        if (file_exists($filePath)) {

            LocalLog::INFO('TaskRunner',"开始运行 $filePath");

            $outname = explode('/',$filePath);
            $outname = explode('.',$outname[count($outname)-1])[0];
    
            include_once $filePath;
            
            // $output_file = "Storage/$outname"."_run.out";
    
            // $cmd_string = "nohup php $filePath $paramString > $output_file 2>&1";
            
            // exec($cmd_string, $out, $ret); //2>是将报错内容定位到这个文件，$ret是一个返回参数，0是正常，1是出错。
    
            // if ($ret == 0) {
    
            //     LocalLog::SUCCESS('TaskRunner',"运行 $filePath 结束！");
            // }
            // else {
    
            //     LocalLog::ERROR('TaskRunner',"运行 $filePath 出错！");
            // }

            // foreach($out as $item) {

            //     LocalLog::INFO("$outname out",$item);
            // }
        }
        else {

            LocalLog::ERROR('task',"文件不存在！");

            return false;
        }
    }

    static public function getAllTasks() {

        $all_files = TaskRunner::dirScanner( APP_ROOT . 'Tasks');
    }

    static public function dirScanner($dir) {

        
    }
}