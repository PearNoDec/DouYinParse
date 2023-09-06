<?php
header("Content-Type:application/json;charset=utf-8");
function print_json($content, $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) {
    print_r(json_encode($content, $options));
}
class DouYinParse{
    function ProcessRun($url){
        if(empty($url)){
            $content = [
                'code' => 203,
                'msg' => "请输入待解析的抖音链接",
                'api_source' => "官方API网:https://api.pearktrue.cn/"
            ];
            print_json($content);
        }
        else{
            $ReturnLocationUrl = $this->location_url($url);
            preg_match('/[0-9]+/', $ReturnLocationUrl, $id);
            if (empty($id)) {
                preg_match('/[0-9]+/', $url, $id);
            }
            $id = $id[0];
            $LocationReal = "https://www.douyin.com/aweme/v1/web/aweme/detail/?device_platform=webapp&aid=6383&channel=channel_pc_web&aweme_id=$id&pc_client_type=1&version_code=190500&version_name=19.5.0&cookie_enabled=true&screen_width=1344&screen_height=756&browser_language=zh-CN&browser_platform=Win32&browser_name=Firefox&browser_version=110.0&browser_online=true&engine_name=Gecko&engine_version=109.0&os_name=Windows&os_version=10&cpu_core_num=16&device_memory=&platform=PC&webid=7158288523463362079&msToken=abL8SeUTPa9-EToD8qfC7toScSADxpg6yLh2dbNcpWHzE0bT04txM_4UwquIcRvkRb9IU8sifwgM1Kwf1Lsld81o9Irt2_yNyUbbQPSUO8EfVlZJ_78FckDFnwVBVUVK";
            $Post_Data = [
                'url' => $LocationReal,
                'user_agent' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Postman/10.16.0 Chrome/100.0.4896.160 Electron/18.3.5 Safari/537.36"
            ];
            $jsonStr = json_encode($Post_Data);
            $ResultXBugUrl = json_decode($this->SendJsonPostData($jsonStr),true)['param'];
            $ResultData = $this->SendGetData($ResultXBugUrl);
            $JsonResult = json_decode($ResultData,true);
            $video_url = $JsonResult['aweme_detail']['video']['play_addr']['url_list'][0];
            if(empty($video_url)){
                $content = [
                    'code' => 201,
                    'msg' => "视频解析失败",
                    'api_source' => "官方API网:https://api.pearktrue.cn/"
                ];
                print_json($content);
            }
            else{
                $content = [
                    'code' => 200,
                    'msg' => "解析成功",
                    'data' => [
                        'author' => $JsonResult['aweme_detail']['author']['nickname'],
                        'uid' => $JsonResult['aweme_detail']['author']['unique_id'], 
                        'like' => $JsonResult['aweme_detail']['statistics']['digg_count'], 
                        'time' => $JsonResult['aweme_detail']["create_time"], 
                        'title' => $JsonResult['aweme_detail']['desc'],
                        'cover' => $JsonResult['aweme_detail']['video']['origin_cover']['url_list'][0],
                        'url' => $JsonResult['aweme_detail']['video']['play_addr']['url_list'][0]
                    ],
                    'api_source' => "官方API网:https://api.pearktrue.cn/"
                ];
                print_json($content);
            }
        }
    }
    function SendJsonPostData($jsonStr)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, "https://tiktok.iculture.cc/X-Bogus");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/116.0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8",
            "Accept-Language: zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2",
            "Content-Type:application/json;charset=utf-8",
            "Connection: keep-alive"
        ]);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $content = [
                'code' => 500,
                'msg' => "访问出错",
                'api_source' => "官方API网:https://api.pearktrue.cn/"
            ];
            print_json($content);
            exit();
        }
        curl_close($ch);
        return $result;
    }
    function SendGetData($url){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Postman/10.16.0 Chrome/100.0.4896.160 Electron/18.3.5 Safari/537.36");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8",
            "Accept-Language: zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2",
            "Referer: https://www.douyin.com/",
            "Connection: keep-alive",
            "Cookie: " # 这里填入你的抖音Cookie值
        ]);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $content = [
                'code' => 500,
                'msg' => "访问出错",
                'api_source' => "官方API网:https://api.pearktrue.cn/"
            ];
            print_json($content);
            exit();
        }
        curl_close($ch);
        return $result;
    }
    function location_url($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $res = curl_exec($ch);
        $final_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        return $final_url;
    }
}
$url = $_REQUEST['url'] ?? "";
$StartParse = new DouYinParse();
$StartParse->ProcessRun($url);
?>