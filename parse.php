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
            "Cookie: ttwid=1%7C0YBAnAwiC5T3U5yJi8RVXEK3DOwF_2vpJ7kVJJZe8HU%7C1666668932%7C21048e6555b73e8801d3956afc6130b4a05ae73a2eefe4d3fef5ef1b61caf0e9; __live_version__=%221.1.1.2586%22; odin_tt=a77b90afad5db31e86fe004b39c5f35423292023ce7837cde82fd1f7fe54278890ce24dc89e09c8a2e55b1f4904950a7b0fca6b4fbff3b549ba6d55a335373ec; pwa2=%223%7C0%7C0%7C0%22; s_v_web_id=verify_lkagpdq1_IuHpxJyS_q6YH_4AvH_8aNH_zhvGPr95Jrc8; passport_csrf_token=301cf539fb735ab77de7e382b0dd93e5; passport_csrf_token_default=301cf539fb735ab77de7e382b0dd93e5; bd_ticket_guard_client_data=eyJiZC10aWNrZXQtZ3VhcmQtdmVyc2lvbiI6MiwiYmQtdGlja2V0LWd1YXJkLWl0ZXJhdGlvbi12ZXJzaW9uIjoxLCJiZC10aWNrZXQtZ3VhcmQtcmVlLXB1YmxpYy1rZXkiOiJCRXhuWUdqREVBa3ErdjRsT2l3anRIWi9HU2hRNXFseWdJMklLanIxM0orRHozYnA0M2pXc3M3N25CUzdnbE5tTXhHbWU3cldoSE9pdkJvVmNnT2JiWFU9IiwiYmQtdGlja2V0LWd1YXJkLXdlYi12ZXJzaW9uIjoxfQ==; passport_assist_user=CkHJzB17Xsy3FUHyNfX2Dyb8IFKKA_0pu1SKYG0OAT_av3ImQyCbEmGJV7b8MJep4l9MjeCRK1FPY9k9yAkVHbIbvhpICjzS68aPlRjIsUzHLIEM-5jMbp9awcdJnkACni5Nnc_PBm4ljAlEqChbF4nYPpn4xyh4kY2hBvRikmXs0sgQ4fq2DRiJr9ZUIgEDbm8-yw%3D%3D; n_mh=13KNPUKNEzoW3A4J-OLRxfal2zj1GbF-vJUFPs3WSIY; sso_uid_tt=2581aab41d03156c0b7fee9c7e865c6c; sso_uid_tt_ss=2581aab41d03156c0b7fee9c7e865c6c; toutiao_sso_user=b2556b53ed5cee89e947b154b17645f1; toutiao_sso_user_ss=b2556b53ed5cee89e947b154b17645f1; sid_ucp_sso_v1=1.0.0-KDhlZjRhMmJhZGU0OTVmOWM0YzBkMTY5ZGNkZmI4NTFjNTk2ODU5OTkKHwiPluCxqYzbAhC29OKmBhjvMSAMMLDIpZkGOAZA9AcaAmhsIiBiMjU1NmI1M2VkNWNlZTg5ZTk0N2IxNTRiMTc2NDVmMQ; ssid_ucp_sso_v1=1.0.0-KDhlZjRhMmJhZGU0OTVmOWM0YzBkMTY5ZGNkZmI4NTFjNTk2ODU5OTkKHwiPluCxqYzbAhC29OKmBhjvMSAMMLDIpZkGOAZA9AcaAmhsIiBiMjU1NmI1M2VkNWNlZTg5ZTk0N2IxNTRiMTc2NDVmMQ; sid_guard=c1d1ac1d22198149dfc6cac74938b14a%7C1691925046%7C5184000%7CThu%2C+12-Oct-2023+11%3A10%3A46+GMT; uid_tt=7e39a426dac7802b2448fa2266ca1b85; uid_tt_ss=7e39a426dac7802b2448fa2266ca1b85; sid_tt=c1d1ac1d22198149dfc6cac74938b14a; sessionid=c1d1ac1d22198149dfc6cac74938b14a; sessionid_ss=c1d1ac1d22198149dfc6cac74938b14a; sid_ucp_v1=1.0.0-KDc4Y2VkZjIyN2JlMDNhYmNhYTFlYTE5ODM1YzI2YjVlZDNmMGY0N2YKGwiPluCxqYzbAhC29OKmBhjvMSAMOAZA9AdIBBoCbHEiIGMxZDFhYzFkMjIxOTgxNDlkZmM2Y2FjNzQ5MzhiMTRh; ssid_ucp_v1=1.0.0-KDc4Y2VkZjIyN2JlMDNhYmNhYTFlYTE5ODM1YzI2YjVlZDNmMGY0N2YKGwiPluCxqYzbAhC29OKmBhjvMSAMOAZA9AdIBBoCbHEiIGMxZDFhYzFkMjIxOTgxNDlkZmM2Y2FjNzQ5MzhiMTRh; LOGIN_STATUS=1; _bd_ticket_crypt_cookie=861cdca903469f36dd23fc1ecfe847c1; __security_server_data_status=1; store-region=us; store-region-src=uid; d_ticket=28acd5a9c6df4227b13582669694acded6ede; __ac_nonce=064ec4f3a00901157c769; __ac_signature=_02B4Z6wo00f01ve8HKgAAIDD6.-iFWbfM-r3jRgAANkQTCm7UjsJOQlMGY7o-iPsCIAe0kuriDaQ15lHcML.nW.cGNWpSBLUJzdr6s8KHRbqh5ywvupCeAKBEHKKbji7hD1-Z0x3DI-n0KKx34; douyin.com; device_web_cpu_core=16; device_web_memory_size=-1; webcast_local_quality=null; publish_badge_show_info=%220%2C0%2C0%2C1693208382348%22; IsDouyinActive=true; home_can_add_dy_2_desktop=%220%22; strategyABtestKey=%221693208382.387%22; stream_recommend_feed_params=%22%7B%5C%22cookie_enabled%5C%22%3Atrue%2C%5C%22screen_width%5C%22%3A1344%2C%5C%22screen_height%5C%22%3A756%2C%5C%22browser_online%5C%22%3Atrue%2C%5C%22cpu_core_num%5C%22%3A16%2C%5C%22device_memory%5C%22%3A0%2C%5C%22downlink%5C%22%3A%5C%22%5C%22%2C%5C%22effective_type%5C%22%3A%5C%22%5C%22%2C%5C%22round_trip_time%5C%22%3A0%7D%22; VIDEO_FILTER_MEMO_SELECT=%7B%22expireTime%22%3A1693813183367%2C%22type%22%3A1%7D; volume_info=%7B%22isUserMute%22%3Afalse%2C%22isMute%22%3Atrue%2C%22volume%22%3A1%7D; my_rd=1; passport_fe_beating_status=true; msToken=ESPx4FwNhcdEvr36-bmhWde9xupU_c64WeeqvvzqzLCtmEsvGPXhkwsKM8miaoC2w8gWSzNAfqxPEju4w3jzopIFompVSmwemq9-z1F8V-2vLNhTxLlYCUVdXkzNj6zM; download_guide=%221%2F20230828%2F0%22; csrf_session_id=3c194edf7f2cee968b0df65f97a11648; msToken=XFIGWeX20IGrrEUGYr_4SR2DPrduwK5zxB3gOp8FfbxW_Ng-w9uNh8wQRUIoPUtkSblL6msqte55jyfcrKPb8eDZekS9Q1P9hkdkPFiV4Ni-l9Vmsr0KgFo5MOkLaBZy; tt_scid=-i-7N5fAMRj8pGg4drGXbjasutdtD4tzIeqRnm6OJ1LoXRRZGl8FNhORnEuY3id.b3b7"
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
