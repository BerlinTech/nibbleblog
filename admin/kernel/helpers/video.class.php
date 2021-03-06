<?php

/*
 * Nibbleblog -
 * http://www.nibbleblog.com
 * Author Diego Najar

 * Last update: 15/07/2012

 * All Nibbleblog code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
*/

class HELPER_VIDEO {

	// Get video info on array
	// If the video does not exist or is invalid, returns false
	public function video_get_info($url, $width = 640, $height = 360)
	{
		global $_TEXT;

		if( $_TEXT->is_substring($url, 'youtube.com') )
		{
			return( $this->video_get_youtube($url, $width, $height) );
		}
		elseif( $_TEXT->is_substring($url, 'vimeo.com') )
		{
			return( $this->video_get_vimeo($url, $width, $height) );
		}

		return false;
	}

	private function video_get_youtube($url, $width = 640, $height = 360)
	{
		global $_TEXT;

		// Youtube ID
		preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $matches);
		$video_id = $matches[1];

		// HEADERS
		$headers = get_headers('http://gdata.youtube.com/feeds/api/videos/'.$video_id);
		if( !$_TEXT->is_substring($headers[0], '200') )
			return(false);

		// GET INFO
		$xml = simplexml_load_file('http://gdata.youtube.com/feeds/api/videos/'.$video_id);
		$media = $xml->children('http://search.yahoo.com/mrss/');

		$info = array();
		$info['id'] = $video_id;
		$info['title'] = (string)$media->group->title;
		$info['description'] = (string)$media->group->description;

		$info['thumb0'] = (string)$media->group->thumbnail[0]->attributes()->url;
		$info['thumb1'] = (string)$media->group->thumbnail[1]->attributes()->url;
		$info['thumb2'] = (string)$media->group->thumbnail[2]->attributes()->url;
		$info['thumb3'] = (string)$media->group->thumbnail[3]->attributes()->url;

		$info['embed'] = '<iframe class="youtube_embed" width="'.$width.'" height="'.$height.'" src="http://www.youtube.com/embed/'.$video_id.'?rel=0" frameborder="0" allowfullscreen></iframe>';

		return($info);
	}

	private function video_get_vimeo($url, $width = 640, $height = 360)
	{
		global $_TEXT;

		preg_match('/vimeo\.com\/([0-9]{1,10})/', $url, $matches);
		$video_id = $matches[1];

		// HEADERS
		$headers = get_headers('http://vimeo.com/api/v2/video/'.$video_id.'.php');
		if( !$_TEXT->is_substring($headers[0], '200') )
			return(false);

		$hash = unserialize(file_get_contents('http://vimeo.com/api/v2/video/'.$video_id.'.php'));

		$info = array();
		$info['id'] = $video_id;
		$info['title'] = $hash[0]['title'];
		$info['description'] = $hash[0]['description'];

		$info['thumb_small'] =  $hash[0]['thumbnail_small'];
		$info['thumb_medium'] =  $hash[0]['thumbnail_medium'];

		$info['embed'] = '<iframe class="vimeo_embed" width="'.$width.'" height="'.$height.'" src="http://player.vimeo.com/video/'.$video_id.'"  frameborder="0" allowFullScreen></iframe>';

		return($info);
	}

}

?>
