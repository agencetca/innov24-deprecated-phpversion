<?php
$conditions="
(lang_key IN ('_bx_spy_profile_has_joined', '_bx_spy_profile_has_rated', '_bx_spy_profile_has_commented', '_bx_spy_profile_friend_accept', '_bx_spywall_message', '_bx_spywall_messageseitu', '_bx_photoalbumshare', 'bx_photo_deluxe_tag', 'bx_photo_deluxe_commentofoto', 'bx_photo_deluxe_commentoalbum'";

if ($spyprofileview=="ON") {$conditions=$conditions.",'_bx_spy_profile_has_viewed'";}
if ($profileupdate=="ON") {$conditions=$conditions.",'_bx_spy_profile_has_edited'";}
if ($photo=="ON") {$conditions=$conditions.", '_bx_photos_spy_added', '_bx_photos_spy_comment_posted', '_bx_photos_spy_rated', '_bx_photo_add_condivisione'";}
if ($video=="ON") {$conditions=$conditions.", '_bx_videos_spy_added', '_bx_videos_spy_rated', '_bx_videos_spy_comment_posted', '_bx_videolocal_add_condivisione', '_bx_videotube_add_condivisione'";}
if ($event=="ON") {$conditions=$conditions.", '_bx_events_spy_post', '_bx_events_spy_join', '_bx_events_spy_rate', '_bx_events_spy_comment', '_bx_events_spy_post_change'";}
if ($group=="ON") {$conditions=$conditions.", '_bx_groups_spy_post', '_bx_groups_spy_post_change', '_bx_groups_spy_join', '_bx_groups_spy_rate', '_bx_groups_spy_comment'";}
if ($site=="ON")  {$conditions=$conditions.", '_bx_sites_poll_add', '_bx_sites_poll_rate', '_bx_sites_poll_commentPost', '_bx_sites_poll_change'";}
if ($poll=="ON")  {$conditions=$conditions.", '_bx_poll_added', '_bx_poll_answered', '_bx_poll_rated', '_bx_poll_commented'";}
if ($ads=="ON")   {$conditions=$conditions.", '_bx_ads_added_spy', '_bx_ads_rated_spy'"; }
 $conditions=$conditions."))";
?>




