<?php
$conditions=" (lang_key IN ('_bx_spy_profile_has_joined', '_bx_spy_profile_has_rated', '_bx_spy_profile_has_edited', '_bx_spy_profile_has_commented', '_bx_spy_profile_friend_accept', '_ibdw_evowall_bx_evowall_message','_ibdw_evowall_bx_evowall_messageseitu', '_ibdw_evowall_bx_url_share','_ibdw_evowall_bx_url_add'";
if ($spyprofileview=="on") {$conditions=$conditions.",'_bx_spy_profile_has_viewed'";}
if ($photo=="on") {$conditions=$conditions.", '_bx_photos_spy_added', '_bx_photos_spy_comment_posted', '_bx_photos_spy_rated', '_bx_photoalbumshare', 'bx_photo_deluxe_commentofoto', 'bx_photo_deluxe_tag', 'bx_photo_deluxe_commentoalbum', '_ibdw_evowall_bx_photo_add_condivisione', '_bx_photo_add_condivisione','_ibdw_photodeluxe_likeadd'";}
if ($video=="on") {$conditions=$conditions.", '_bx_videos_spy_added', '_bx_videos_spy_rated', '_bx_videos_spy_comment_posted', '_ibdw_evowall_bx_video_add_condivisione'";}
if ($event=="on") 
{
 if ($eventmodule=="Boonex") {$conditions=$conditions.", '_bx_events_spy_post', '_bx_events_spy_join', '_bx_events_spy_rate', '_bx_events_spy_comment', '_bx_events_spy_post_change', '_ibdw_evowall_bx_event_add_condivisione'";}
 else {$conditions=$conditions.", '_ue30_event_spy_post', '_ue30_event_spy_join', '_ue30_event_spy_rate', '_ue30_event_spy_comment', '_ue30_event_spy_post_change', '_ue30_event_add_condivisione'";}
}
if ($group=="on") {$conditions=$conditions.", '_bx_groups_spy_post', '_bx_groups_spy_post_change', '_bx_groups_spy_join', '_bx_groups_spy_rate', '_bx_groups_spy_comment', '_ibdw_evowall_bx_gruppo_add_condivisione'";}
if ($site=="on") {$conditions=$conditions.", '_bx_sites_poll_add', '_bx_sites_poll_rate', '_bx_sites_poll_commentPost', '_bx_sites_poll_change', '_ibdw_evowall_bx_site_add_condivisione'";}
if ($poll=="on") {$conditions=$conditions.", '_bx_poll_added', '_bx_poll_answered', '_bx_poll_rated', '_bx_poll_commented', '_ibdw_evowall_bx_poll_add_condivisione'";}
if ($ads=="on") {$conditions=$conditions.", '_ibdw_evowall_bx_ads_add_condivisione', '_bx_ads_added_spy', '_bx_ads_rated_spy'";}
if ($blogs=="on") {$conditions=$conditions.", '_ibdw_evowall_bx_blogs_add_condivisione', '_bx_blog_added_spy', '_bx_blog_rated_spy', '_bx_blog_commented_spy'";}
if ($sounds=="on") {$conditions=$conditions.", '_ibdw_evowall_bx_sounds_add_condivisione', '_bx_sounds_spy_added', '_bx_sounds_spy_comment_posted', '_bx_sounds_spy_rated'";}
if ($modzzzproperty=="on") {$conditions=$conditions.", '_modzzz_property_spy_post', '_modzzz_property_spy_post_change', '_modzzz_property_spy_join', '_modzzz_property_spy_rate', '_modzzz_property_spy_comment','_ibdw_evowall_modzzz_property_share'";}
if ($ue30locations=="on") {$conditions=$conditions.", '_ue30_location_spy_post', '_ue30_location_spy_post_change', '_ue30_location_spy_join', '_ue30_location_spy_rate', '_ue30_location_spy_comment','_ibdw_evowall_ue30_locations_add_condivisione'";}
if ($modzzzclubs=="on") {$conditions=$conditions.", '_modzzz_club_spy_post', '_modzzz_club_spy_post_change', '_modzzz_club_spy_join', '_modzzz_club_spy_rate', '_modzzz_club_spy_comment', '_ibdw_evowall_bx_clubs_add_condivisione'";}
if ($modzzzpetitions=="on") {$conditions=$conditions.", '_modzzz_petitions_spy_post', '_modzzz_petitions_spy_post_change', '_modzzz_petitions_spy_join', '_modzzz_petitions_spy_rate', '_modzzz_petitions_spy_comment', '_ibdw_evowall_bx_petitions_add_condivisione'";}
if ($modzzzpets=="on") {$conditions=$conditions.", '_modzzz_pets_spy_post', '_modzzz_pets_spy_post_change', '_modzzz_pets_spy_rate', '_modzzz_pets_spy_comment', '_ibdw_evowall_bx_pets_add_condivisione'";}
if ($modzzzbands=="on") {$conditions=$conditions.", '_modzzz_bands_spy_post', '_modzzz_bands_spy_post_change', '_modzzz_bands_spy_join', '_modzzz_bands_spy_rate', '_modzzz_bands_spy_comment', '_ibdw_evowall_bx_bands_add_condivisione'";}
if ($modzzzschools=="on") {$conditions=$conditions.", '_modzzz_schools_spy_post', '_modzzz_schools_spy_post_change', '_modzzz_schools_spy_join', '_modzzz_schools_spy_rate', '_modzzz_schools_spy_comment', '_ibdw_evowall_bx_schools_add_condivisione'";}
if ($modzzznotices=="on") {$conditions=$conditions.", '_modzzz_notices_spy_post', '_modzzz_notices_spy_post_change', '_modzzz_notices_spy_rate', '_modzzz_notices_spy_comment', '_ibdw_evowall_bx_notices_add_condivisione'";}
if ($modzzzclassified=="on") {$conditions=$conditions.", '_modzzz_classified_spy_post', '_modzzz_classified_spy_post_change', '_modzzz_classified_spy_rate', '_modzzz_classified_spy_comment', '_ibdw_evowall_bx_classified_add_condivisione'";}
if ($modzzznews=="on") {$conditions=$conditions.", '_modzzz_news_spy_post', '_modzzz_news_spy_post_change', '_modzzz_news_spy_rate', '_modzzz_news_spy_comment', '_ibdw_evowall_bx_news_add_condivisione'";}
if ($modzzzjobs=="on") {$conditions=$conditions.", '_modzzz_jobs_spy_join', '_modzzz_jobs_spy_post', '_modzzz_jobs_spy_post_change', '_modzzz_jobs_spy_rate', '_modzzz_jobs_spy_comment','_ibdw_evowall_bx_jobs_add_condivisione'";}
if ($modzzzlist=="on") {$conditions=$conditions.", '_modzzz_listing_spy_join', '_modzzz_listing_spy_post', '_modzzz_listing_spy_post_change', '_modzzz_listing_spy_rate', '_modzzz_listing_spy_comment','_ibdw_evowall_bx_listing_add_condivisione'";}
$conditions=$conditions."))";
?>