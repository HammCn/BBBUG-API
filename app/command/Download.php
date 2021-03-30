<?php

declare (strict_types = 1);

namespace app\command;

use think\console\Input;
use think\console\Output;

class Download extends BaseCommand
{
    protected function configure()
    {
        // 指令配置
        $this->setName('Download')
            ->setDescription('StartAdmin Test Command');
    }

    protected function execute(Input $input, Output $output)
    {
        // cache('song_downloaded_list',null);
        // cache('song_waiting_download_list',null);
        // return;
        while (true) {
            $cacheList = cache('song_waiting_download_list') ?? [];
            if (count($cacheList) > 0) {
                if ($cacheList[0]['url']) {
                    $url = $cacheList[0]['url'];
                    $mid = $cacheList[0]['mid'];
                    try{
                        file_put_contents(__dir__ . "/../../public/music/" . $mid . ".jpg", file_get_contents($url));
                        
                        $downloaded_song_list = cache('song_downloaded_list') ?? [];
                        $isExist = false;
                        foreach ($downloaded_song_list as $_mid) {
                            if ($mid == $_mid) {
                                $isExist = true;
                                break;
                            }
                        }
                        if (!$isExist) {
                            array_push($downloaded_song_list, $mid);
                        }
                        cache('song_downloaded_list', $downloaded_song_list);
                    }catch(\Exception $e){
                        
                    }
                    array_shift($cacheList);
                    cache('song_waiting_download_list', $cacheList); //删掉已下载的文件item
                    cache('song_download_mid_' . $mid, time()); //缓存当前时间

                } else {
                    array_shift($cacheList);
                    cache('song_waiting_download_list', $cacheList);
                }
            }
            $downloaded_song_list = cache('song_downloaded_list') ?? [];
            // print_r($downloaded_song_list);
            for ($i = 0; $i < count($downloaded_song_list); $i++) {
                $_mid = $downloaded_song_list[$i];
                $songCache = cache('song_download_mid_' . $_mid) ?? false;
                // print_r($songCache);
                if ($songCache) {
                    if (time() - $songCache > 600) {
                        echo "超时" . PHP_EOL;
                        $fileName = __dir__ . "/../../public/music/" . $_mid . ".jpg";
                        if (file_exists($fileName)) {
                            unlink($fileName);
                            cache('song_download_mid_' . $_mid, null);
                            array_splice($downloaded_song_list, $i, 1);
                            cache('song_downloaded_list', $downloaded_song_list);
                        }
                    } else {
                        // echo "有效" . PHP_EOL;
                    }
                } else {
                    // echo "下载已移出" . PHP_EOL;
                }
            }
            sleep(1);
        }
    }
}
