<? 
/**********************************************************************************
*                            IBDW EvoWall for Dolphin Smart Community Builder
*                              -------------------
*     begin                : July 23 2012
*     copyright            : (C) 2012 IlBelloDelWEB.it di Ferraro Raffaele Pietro
*     website              : http://www.ilbellodelweb.it
* This file was created but is NOT part of Dolphin Smart Community Builder 7
*
* IBDW EvoWall is not free and you cannot redistribute and/or modify it.
* 
* IBDW EvoWall is protected by a commercial software license.
* The license allows you to obtain updates and bug fixes for free.
* Any requests for customization or advanced versions can be requested 
* at the email info@ilbellodelweb.it. You can modify freely only your language file
* 
* For more details see license.txt file; if not, write to info@ilbellodelweb.it
**********************************************************************************/

require_once(BX_DIRECTORY_PATH_CLASSES . 'BxDolConfig.php');
class evowallConfig extends BxDolConfig 
{
 	var $iKeyCode;
	var $iDefaultProfilePrivacy;
	var $iDefaultAccountPrivacy;
	var $iDefaultHomePrivacy;
	var $iTemplateColor;
    var $iAllowPhotos;
	var $iAllowVideos;
	var $iAllowGroups;
	var $iAllowEvents;
	var $iEventMod;
	var $iAllowSites;
	var $iAllowPolls;
	var $iAllowAds;
	var $iAllowBlogs;
	var $iAllowSounds;
	var $iModzzzProperty;
	var $iUE30Locations;
	var $iTypeofOrder;
	var $iDaysMostPopular;
	var $iMessageLenght;
	var $iCommentLenght;
	var $iCommentPrevNum;
	var $iCommentNum;
	var $iNewLine;
	var $iAvatarType;
	var $iNameFormat;
	var $iDisplayProfileUpdate;
	var $iCommentOrder;
	var $iWallPhotoName;
	var $iWallVideoName;
	var $iWallDefaultPrivacy;
	var $iPhotoLarge;
	var $iPhotoAutoZoom;
	var $iPhotoSizeWidth;
	var $iVideoSizeWidth;
	var $iVideoSizeHeight;
	var $iDisplayNewsNumber;
	var $iProfileViewedBy;
	var $iDenyAccessToUnconfirmed;
	var $iRefreshType;
	var $iRefreshrate;
	var $iAutoScroll;
	var $iAutoScrollTime;
	var $iHideMoreNewsButton;
	var $iDateFormat;
	var $iOffset;
	var $iWelcomeMessage;
	var $iWelcomeNPost;
	var $iWelcomeCLife;
	var $iUrlPlugin;
	var $iGrouping;
	var $iTimingSimilar;
	var $iPhotoMaxPreview;
	var $iPhotoRegularM;
	var $iPhotoFlashM;
	var $iPhotoOtherM;
	var $iVideoYoutubeM;
	var $iVideoFalshM;
	var $iVideoOtherM;
	var $iDefaultPhotoM;
	var $iDefaultVideoM;
	var $iAllowFacebook;
	var $iAllowGoogle;
	var $iAllowTwitter;
	var $iAllowLinkedIn;
	var $iAllowPinterest;
	var $iAllowBaido;
	var $iAllowWeibo;
	var $iAllowQzone;
	
	function evowallConfig($aModule) 
	{
	    parent::BxDolConfig($aModule);
		$this -> iKeyCode = getParam('LicenseKey');
		$this -> iDefaultProfilePrivacy = getParam('DefaultProfilePrivacy');
		$this -> iDefaultAccountPrivacy = getParam('DefaultAccountPrivacy');
		$this -> iDefaultHomePrivacy = getParam('DefaultHomePrivacy');
		$this -> iTemplateColor = getParam('TemplateColor');
        $this -> iAllowPhotos  = getParam('AllowPhotos') ? true : false;
        $this -> iAllowVideos  = getParam('AllowVideos') ? true : false;
        $this -> iAllowGroups  = getParam('AllowGroups') ? true : false;
        $this -> iAllowEvents  = getParam('AllowEvents') ? true : false;
		$this -> iEventMod = getParam('EventMod');
        $this -> iAllowSites   = getParam('AllowSites') ? true : false;
        $this -> iAllowPolls   = getParam('AllowPolls') ? true : false;
		$this -> iAllowAds     = getParam('AllowAds') ? true : false;
		$this -> iAllowBlogs   = getParam('AllowBlogs') ? true : false;
		$this -> iAllowSounds  = getParam('AllowSounds') ? true : false;
		$this -> iModzzzProperty  = getParam('ModzzzProperty') ? true : false;
		$this -> iUE30Locations  = getParam('UE30Locations') ? true : false;
		$this -> iTypeofOrder = getParam('TypeofOrder');
		$this -> iDaysMostPopular = (int) getParam('DaysMostPopular');
		$this -> iMessageLenght = (int) getParam('MessageLenght');
    	$this -> iCommentLenght  = (int) getParam('CommentLenght');
		$this -> iCommentPrevNum  = (int) getParam('CommentPrevNum');
		$this -> iCommentNum  = (int) getParam('CommentNum');
		$this -> iNewLine  = getParam('NewLine') ? true : false;
		$this -> iAvatarType = getParam('AvatarType');
		$this -> iNameFormat = getParam('NameFormat');
		$this -> iDisplayProfileUpdate = getParam('DisplayProfileUpdate') ? true : false;
		$this -> iCommentOrder = getParam('CommentOrder');
		$this -> iWallPhotoName = preg_replace('/\s{2,}/',' ', getParam('WallPhotoName'));
		$this -> iWallVideoName = preg_replace('/\s{2,}/',' ', getParam('WallVideoName'));
		$this -> iWallDefaultPrivacy = getParam('WallDefaultPrivacy');
		$this -> iPhotoSizeWidth = (int) getParam('PhotoSizeWidth');
		$this -> iPhotoLarge = getParam('PhotoLarge');
		$this -> iPhotoAutoZoom = getParam('PhotoAutoZoom');
		$this -> iVideoSizeWidth = (int) getParam('VideoSizeWidth');
		$this -> iVideoSizeHeight = (int) getParam('VideoSizeHeight');
		$this -> iDisplayNewsNumber = (int) getParam('DisplayNewsNumber');
		$this -> iProfileViewedBy = getParam('ProfileViewedBy') ? true : false;
		$this -> iDenyAccessToUnconfirmed = getParam('DenyAccessToUnconfirmed') ? true : false;
		$this -> iRefreshType = getParam('RefreshType');
		$this -> iRefreshrate = (int) getParam('Refreshrate');
		$this -> iAutoScroll = getParam('AutoScroll') ? true : false;
		$this -> iAutoScrollTime = (int) getParam('AutoScrollTime');
		$this -> iHideMoreNewsButton = getParam('HideMoreNewsButton') ? true : false;
		$this -> iDateFormat = getParam('DateFormat');
		$this -> iOffset = (int) getParam('Offset');
		$this -> iWelcomeMessage = getParam('WelcomeMessage') ? true : false;
		$this -> iWelcomeNPost = (int) getParam('WelcomeNPost');
		$this -> iWelcomeCLife = (int) getParam('WelcomeCLife');
		$this -> iUrlPlugin = getParam('UrlPlugin') ? true : false;
		$this -> iUrlPlugin = getParam('UrlPlugin') ? true : false;
		$this -> iGrouping = getParam('Grouping') ? true : false;
		$this -> iTimingSimilar = getParam('TimingSimilar');
		$this -> iPhotoMaxPreview = (int) getParam('PhotoMaxPreview');
		$this -> iPhotoFlashM = getParam('PhotoFlashM') ? true : false;
		$this -> iPhotoOtherM = getParam('PhotoOtherM') ? true : false;
		$this -> iVideoYoutubeM = getParam('VideoYoutubeM') ? true : false;
		$this -> iVideoFalshM = getParam('VideoFalshM') ? true : false;
		$this -> iVideoOtherM = getParam('VideoOtherM') ? true : false;
		$this -> iDefaultPhotoM = getParam('DefaultPhotoM');
		$this -> iDefaultVideoM = getParam('DefaultVideoM');
		$this -> iAllowFacebook  = getParam('AllowFacebook') ? true : false;
		$this -> iAllowGoogle  = getParam('AllowGoogle') ? true : false;
		$this -> iAllowTwitter  = getParam('AllowTwitter') ? true : false;
		$this -> iAllowLinkedIn  = getParam('AllowLinkedIn') ? true : false;
		$this -> iAllowPinterest  = getParam('AllowPinterest') ? true : false;
		$this -> iAllowBaido  = getParam('AllowBaido') ? true : false;
		$this -> iAllowWeibo  = getParam('AllowWeibo') ? true : false;
		$this -> iAllowQzone  = getParam('AllowQzone') ? true : false;
	}
}?>