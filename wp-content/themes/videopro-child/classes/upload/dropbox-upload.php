<?php

/**
 *  Version: 1.0.0
 *  Text Domain: WPStylish-wp-cloud
 * @since 1.0.0
 */
class VideoPro_CLOUD_DROPBOX_UPLOAD
{

    public function __construct()
    {

    }

    function wp_cloud_dropbox_upload($post_id)
    {
        $video_file_url = get_post_meta($post_id, 'tm_video_file', true);

        $path_parts = pathinfo($video_file_url);
        $fileName = $path_parts['basename'];
        $fileType = $path_parts['extension'];

        $all_files = array();

        $path = $this->dropbox_upload_file($fileName, $video_file_url);
        $dropbox_file = json_decode($this->dropbox_get_file_url($path));

        if ($dropbox_file && isset($dropbox_file->url)) {
            $all_files['fileName'] = $fileName;
            $all_files['fileType'] = $fileType;
            $all_files['fileURL'] = rtrim($dropbox_file->url, '0') . '1';
        }

        return $all_files;
    }

    function dropbox_upload_file($file_name, $file_dir)
    {
        $upload_url = 'https://content.dropboxapi.com/2/files/upload';
        $path = '/' . $file_name;

        $dropbox_args = json_encode(array(
            'path' => $path,
            'autorename' => true,
        ));
        $header = array(
            "Content-Type: application/octet-stream",
            "Dropbox-API-Arg: $dropbox_args"
        );

        $file_content = file_get_contents($file_dir);

        $this->dropbox_build_curl($upload_url, $header, $file_content);

        return $path;
    }

    function dropbox_get_file_url($path)
    {
        $url = 'https://api.dropboxapi.com/2/sharing/create_shared_link_with_settings';

        $header = array(
            "Content-Type: application/json"
        );

        $data = json_encode(array(
            'path' => $path
        ));

        return $this->dropbox_build_curl($url, $header, $data);

    }

    function get_account()
    {
        $url = 'https://api.dropboxapi.com/2/users/get_current_account';

        $account = $this->dropbox_build_curl($url);

        return $account;
    }

    function dropbox_build_curl($url, $headers = array(), $content = '')
    {

        $token = get_option('wp_cloud_dropbox_token');

        if ($token == false) {
            return false;
        }

        $header = array_merge($headers, array(
            "Authorization: Bearer $token",
        ));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        //curl_setopt($ch, CURLOPT_POST,true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

        if (!empty($content)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        }

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
}

$GLOBALS['videoPro_cloud_dropbox_upload'] = new VideoPro_CLOUD_DROPBOX_UPLOAD();
