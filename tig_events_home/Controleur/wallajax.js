



// AJAX DU WALL
function loadMoreContent(){
$.get('tig_events_home/Modele/wall.php', function(data) {
if (data.length != 0) {
$('#wall li:last').after(data);
}
else {
$('#more_view').remove();
}
});
};
$(window).scroll(function() {
// Modify to adjust trigger point. You may want to add content
// a little before the end of the page is reached. You may also want
// to make sure you can't retrigger the end of page condition while
// content is still loading.
if ($(window).scrollTop() == $(document).height() - $(window).height()) {
loadMoreContent();
}
});





