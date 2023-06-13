## OSTICKET RATING
This plugin can be installed into your osticket installation by adding the content into the plugin folder.

Features added:

**Rating table**

*Filtered*
![enter image description here](https://cdn.discordapp.com/attachments/951845560820838412/1118084338316214292/Screenshot_2023-06-13_at_09.46.13.png)
*Not filtered*
![enter image description here](https://cdn.discordapp.com/attachments/951845560820838412/1118084338530127942/Screenshot_2023-06-13_at_09.46.37.png)
**Configuration page**

![enter image description here](https://cdn.discordapp.com/attachments/951845560820838412/1118084561943932928/Screenshot_2023-06-13_at_09.48.41.png)
Result page: page you want to show when everything goes well
Error pages: two different custom links for errors
Generic error: page to show technical errors

## Installation steps

!!*Be sure to have dispatcher.php into your osticket installation under root folder --> /scp, otherwise it is not going to work.*!!

 - Add plugin under the correct folder
 - Press install when asked for plugin installation
 - in order to add a new rating make the user click a button that calls a similar canned response

    [http://localhost:8888/include/plugins/osticket-rating-plugin/insertPage/resultpage.php?rating=3&ticket=2&number=112501&staff_id=1&topic_id=2](http://localhost:8888/include/plugins/osticket-rating-plugin/insertPage/resultpage.php?rating=3&ticket=2&number=112501&staff_id=1&topic_id=2 "http://localhost:8888/include/plugins/osticket-rating-plugin/insertPage/resultpage.php?rating=3&ticket=2&number=112501&staff_id=1&topic_id=2")
    
    Pay attention to these parameters:
    rating: rating given by the user
    ticket: ticket id
    number: ticket number
	staff_id: user staff assignee
	topic_id: ticket topic
	
 - Once user clicks the db table is populated with is rating and should appear in the rating table showed before