=== Bradmax Player ===
Contributors: kostalski
Tags: video, html5, video streaming, HLS, MPEG-DASH, MS Smooth Streaming, embed video, responsive, subtitles, wpvideo, google analytics, google analytics video
Requires at least: 4.2
Tested up to: 5.2.3
Stable tag: 1.1.8
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Author URI: https://bradmax.com/
Author: Bradmax
Version: 1.1.8

Embed video stream easily in WordPress using Bradmax Player. Use responsive HTML5 video player for playing HLS, MPEG-DASH, MS Smooth Streaming streams.

== Description ==

[Bradmax Player](https://bradmax.com/site/en/player) is a plugin, which supports video streams playback on desktops and mobile devices. If you have access to video streaming in formats:
- HLS
- MPEG-DASH
- MS Smooth Streaming
or simple mp4, webM, ogg files, then you can watch them on your site with Bradmax Player. It is even supporting HLS or MS Smooth Streaming playback on platforms / devices, which
usually not support them. In such cases video is "transconded" on-fly in your browser during playback.

Player support also:

* poster image - Custom image from video, which is displayed on player before playback.
* subtitles - Embedded in HLS, MPEG-DASH, MS Smooth Streaming video stream or from external files in SRT, VTT file formats.
* basic video statistics for Google Analytics - Just paste your Google Analytics tracker id into player settings for collecting information about video views and watched time.

= Requirements =

* A self-hosted website running on WordPress CRM.

= Bradmax Player Features =

* Embed video streams into a post/page or anywhere on your WordPress site (supported streaming formats HLS, MPEG-DASH, MS Smooth Streaming).
* Embed video files (MP4, WebM, Ogg) into your page.
* Embed responsive videos for a better user experience while viewing from a mobile device.
* Embed videos with poster images.
* Automatically play a video when the page is rendered.
* Embed videos uploaded to your WordPress media library using direct links in the shortcode.
* No setup required, simply install and start embedding videos.
* Lightweight and compatible with the latest version of WordPress
* Clean and sleek player with no watermark.
* Player customisation is available (change skin, colors, logo, etc.). It requires only sign-up on https://bradmax.com/site/en/signup . It's free and basic version of player is also free.
* Embed video with subtitles (subtitles loaded from stream or from SRT, VTT files).
* Collect basic statistics about video playback in your Google Analytics account.

= Bradmax Player Plugin Usage =

In order to embed a video create a new post/page and use the following shortcode:

`[bradmax_video url="https://bradmax.com/static/video/tos/big_buck_bunny.m3u8" duration="596" poster="https://bradmax.com/static/images/startsplash.jpg"]`

* "url" is the location of your streaming. You need to replace the sample URL with the actual URL of the video stream.
* "duration" contain length in seconds of video, so it can be displayed on player before staring playback.
* "poster" is location of poster image, which should be displayed on player. Replace sample URL with link of your image.

= Video playback statistics with Google Analytics =

Player can collect basic statistics for video playback. You just need to copy your "Tracking ID" from Google Analytics page into player settings.

For finding "Tracking ID" please open: [Google Analytics](https://analytics.google.com) > Admin > Tracking Info > Tracking Code .

"Tracker ID" is code having form "UA-XXXXXXXX-X", where X is 0-9 digit and you have to copy it into ga_tracker_id video shortcode option (see section below).

Player collects video playback details as "Events" in your Google Analytics account. There are available in sections:

* Google Analytics panel > Real-Time > Events
* Google Analytics panel > Behaviour > Events

Player is sending events:

* event category: view , event action: started (send on starting video playback)
* event category: player event, event action: playing/paused (send on play/pause video)
* event category: progress seconds, event action: progress seconds (send every 10 sec of playback)

For each media distinction in statistics you have to specify in video shortcode option "media_id". Then each event got additionaly "Event Label" with data provided from media_id parameter. media_id can be any text, which you want to define, but it is recomended to keep it short.

= Video Shortcode Options =

The following options are supported in the shortcode.

**Autoplay**

Causes the video file to automatically play when the page loads.
Note: Currenlty this option is working only on desktop devices with muted sound (see "Mute" shortcode). On mobile devices (phones, tablets, etc.) this option is not working.
It is platform limitation and clicking on video is required for starting playback.

`[bradmax_video url="http://example.com/hls_stream.m3u8" autoplay="true"]`

**Mute**

Causes the video starts with muted sound. This option is usefull for starting video automatically with "autoplay" option.

`[bradmax_video url="http://example.com/hls_stream.m3u8" autoplay="true" mute="true"]`

**Duration**

Defines length of video stream in seconds. Can contain fraction of second. It is required for displaying duration of video before staring playback.

`[bradmax_video url="http://example.com/hls_stream.m3u8" duration="100.1"]`

**Poster**

Defines image to show as placeholder before the video plays.

`[bradmax_video url="http://example.com/hls_stream.m3u8" poster="http://example.com/wp-content/uploads/poster.jpg"]`

**Class**

Defines CSS class, which should be added into player box on page (customizing view on WordPress page).

`[bradmax_video url="http://example.com/hls_stream.m3u8" class="my-custom-player-css-class"]`

**Style**

Defines CSS style string, which should be added into player for on page (customizing view on WordPress page).

`[bradmax_video url="http://example.com/hls_stream.m3u8" style="width:400px;height:200px;border:solid 1px gray"]`

**Subtitles**

Defines list of subtitles files (one file per language) for video. Subtitles files has to be in SRT or VTT format (file extension *.srt or *.vtt). Format for subtitles list subtitles="LANG_CODE=FILE_LINK LANG_CODE=FILE_LINK ...", where LANG_CODE is two letter language code (ISO 639-1 standard - https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes) for defining subtitles language. FILE_LINK is link to file stored on some HTTP server, which player will be able to download during playback.

Working example (subtitles in Czech language):

`[bradmax_video url="https://bradmax.com/static/video/tos/tesla/tesla.m3u8" subtitles="cz=https://bradmax.com/static/video/tos/tesla/tesla_cz.srt"]`

Example with multiple languages for video:

`[bradmax_video url="http://example.com/hls_stream.m3u8" subtitles="en=https://example.com/subtitles_en.srt cz=https://example.com/subtitles_cz.srt sk=https://example.com/subtitles_sk.srt"]`

**ga_tracker_id**

Defines Google Analytics tracker id. When defined video playback is tracked in your Google Analytics account in "Events" sections.

"Tracker ID" is code having form "UA-XXXXXXXX-X", where X is 0-9 digit and is located in [Google Analytics](https://analytics.google.com) > Admin > Tracking Info > Tracking Code.

Example:

`[bradmax_video url="http://example.com/hls_stream.m3u8" ga_tracker_id="UA-XXXXXXXX-X" media_id="my example stream"]`

**media_id**

This parameter is used, when Google Analytics plugin is active (see ga_tracker_id video shortcode). It is used for each media distinction, so for each different video diferent value should be provided. It can be any text, but it is recomended to keep it short.

== Installation ==

1. Go to the Add New plugins screen in your WordPress Dashboard
2. Click the upload tab
3. Browse for the plugin file (bradmax-player.zip) on your computer.
4. Click "Install Now" and then hit the activate button.

Or install directly from WordPress Plugin Directory.

== Frequently Asked Questions ==

= Can this plugin be used to embed video streams in WordPress? =

Yes.

= Is HLS supported by this player =

Yes, and what more HLS streams can be displayed even on desktop devices, which natively not support HLS streams.

= Are the videos embedded by this plugin playable on iOS devices? =

Yes.

= Can I embed responsive videos using this plugin? =

Yes.


== Upgrade Notice ==
none

== Changelog ==

= 1.1.8 =

* Upgrading default player to v2.7.14 (HLS decoder update, UI fixes for MS Edge, MS Explorer browsers and for small screens on mobile devices).

= 1.1.7 =

* Upgrading default player to v2.7.0 (HLS decoder update, better autoplay behaviour, when autoplay blocked by browser).

= 1.1.6 =

* Support to "mute" video with video short code. 
* Upgrading default player to v2.6.1 (new better HLS decoder; possibility to "mute" video by configuration).

= 1.1.5 =

* Solving conflict between default bradmax player and Youtube embed script (upgrading player to v2.5.12).

= 1.1.4 =

* Plugin readme.txt formatting correction.

= 1.1.3 =

* Upgrading default JavaScript player file from version 2.5.5 to 2.5.9 (bugfixes and better HLS and MPEG-DASH live streaming support).
* Implementing Google Analytics video statistics by video shortcode option.

= 1.1.2 =

* Correcting subtitles documentation in readme.txt file.

= 1.1.1 =

* Upgrading default JavaScript player file from version 2.4.11 to 2.5.5 (bugfixes and new features).
** SRT VTT subtitles files support for player.
** Better HLS live stream support.
** Solving problems with HLS playback on Android 6.0.

= 1.0.2 =

* Upgrading default JavaScript player file from version 2.4.2 to 2.4.11 (bugfixes and better support for HLS).

= 1.0.1 =

* First version of WordPress plugin.
