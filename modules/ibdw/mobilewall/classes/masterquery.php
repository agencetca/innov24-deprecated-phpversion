<?
include('mqconditions.php');
if($personal_query == 1) { 
$query = "SELECT * FROM 
(
(SELECT bx_spy_data.id,bx_spy_data.sender_id,bx_spy_data.recipient_id,bx_spy_data.lang_key,bx_spy_data.params,bx_spy_data.date, Profiles.Avatar FROM bx_spy_data INNER JOIN Profiles ON bx_spy_data.sender_id = Profiles.ID WHERE bx_spy_data.sender_id=$userid AND".$conditions.")

UNION

(SELECT bx_spy_data.id,bx_spy_data.sender_id,bx_spy_data.recipient_id,bx_spy_data.lang_key,bx_spy_data.params,bx_spy_data.date, Profiles.Avatar FROM bx_spy_data INNER JOIN Profiles ON bx_spy_data.sender_id = Profiles.ID WHERE bx_spy_data.recipient_id=$userid AND".$conditions.")
) 
AS mytaby"; }

else { 


$query = "
SELECT * FROM (
(
SELECT bx_spy_data.id,bx_spy_data.sender_id,bx_spy_data.recipient_id,bx_spy_data.lang_key,bx_spy_data.params,bx_spy_data.date,Profiles.Avatar
FROM (bx_spy_data INNER JOIN Profiles ON bx_spy_data.sender_id = Profiles.ID) WHERE bx_spy_data.sender_id=$mioid
)
UNION
(
SELECT BSD.id, BSD.sender_id, BSD.recipient_id, BSD.lang_key, BSD.params, 
BSD.date, P.Avatar FROM bx_spy_data BSD JOIN (SELECT Profile AS sender_id 
FROM sys_friend_list SFL WHERE id = $mioid AND SFL.Check = 1 UNION SELECT id AS 
sender_id FROM sys_friend_list SFL WHERE Profile = $mioid AND SFL.Check = 1) SR 
USING (sender_id) JOIN Profiles P ON BSD.sender_id = P.ID WHERE 
BSD.recipient_id = $mioid
)
UNION
(
SELECT BSD.id, BSD.sender_id, BSD.recipient_id, BSD.lang_key, BSD.params, 
BSD.date, P.Avatar FROM bx_spy_data BSD JOIN (SELECT Profile AS sender_id 
FROM sys_friend_list SFL WHERE id = $mioid AND SFL.Check = 1 UNION SELECT id AS 
sender_id FROM sys_friend_list SFL WHERE Profile = $mioid AND SFL.Check = 1) SR 
USING (sender_id) JOIN Profiles P ON BSD.sender_id = P.ID WHERE 
BSD.recipient_id = 0
)
UNION
(
SELECT bx_spy_data.id,bx_spy_data.sender_id,bx_spy_data.recipient_id,bx_spy_data.lang_key,bx_spy_data.params,bx_spy_data.date,Profiles.Avatar
FROM ((bx_spy_data INNER JOIN Profiles ON bx_spy_data.sender_id = Profiles.ID) INNER JOIN bx_spy_friends_data ON bx_spy_data.id=bx_spy_friends_data.event_id)
WHERE (bx_spy_friends_data.friend_id=$mioid)
)
UNION
(
SELECT BSD.id, BSD.sender_id, BSD.recipient_id, BSD.lang_key, BSD.params, 
BSD.date, P.Avatar FROM bx_spy_data BSD JOIN (SELECT Profile AS sender_id 
FROM sys_friend_list SFL WHERE id = $mioid AND SFL.Check = 1 UNION SELECT id AS 
sender_id FROM sys_friend_list SFL WHERE Profile = $mioid AND SFL.Check = 1) SR 
USING (sender_id) JOIN Profiles P ON BSD.sender_id = P.ID WHERE 
BSD.recipient_id = BSD.sender_id
)
)
AS mytaby WHERE".$conditions;     }
$query=$query." ORDER BY mytaby.id DESC LIMIT $inizioquery,$limite";
?>