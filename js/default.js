$(document).ready(function()
{
	$("#loading").hide();
	$(document).ajaxStart(function()
	{
		$("#loading").show();
	});
	
	$(document).ajaxStop(function()
	{
		$("#loading").hide();
	});
});

/*
 * Joue l'effet passé en paramètre sur l'objet passé en paramètre
 */
function playEffect(target,effect1)
{
	_class=$(target).attr("class");
	$(target).removeClass();
	$(target).addClass("animated "+effect1);
	$(target).addClass(_class);
}

/*
 * Affiche le contenu de la page de présentation du service
 */
function present()
{
	$("#content").html("<br><br><br><h2>What's <font color='#50BCE3'>Tw|irit</font> ?</h2>The name <b><font color='#50BCE3'>Tw|irit</font></b> is a two-word's contraction: \"<u>Tw</u>itter\" and \"Sp<u>irit</u>\".<br>Indeed, this service allows you to tune your Twitter profile's picture depending on your mood, the way you feel.<br><h2>How does it work ?</h2><font color='#50BCE3'>Tw|irit</font> is really simple to use.<br>In fact, when, using <font color='#50BCE3'>our service</font>, you will update your Twitter timeline with a tweet containing your <b>#mood's hashtag</b> (like #happy or #angry), we will change automatically your Twitter account's picture  with one of the pictures you have uploaded to <font color='#50BCE3'>Tw|irit</font>.<br>But, first of all, to use <font color='#50BCE3'>Tw|irit</font>, you just have to click on the button below and to allow <font color='#50BCE3'>our service</font> to read your tweets (To search the #mood hashtags) and in order to change your profile's pic with the pictures you will set.<br><br><br><br><br><br><br><center><a class='button' style='width:300px;height:100px;line-height:100px;vertical-align:middle;'><div style='width:100%;height:100%' id='button'>Link with twitter >></div></a><br><br>When your Twitter account will be linked, you will can choose,<br>upload and associate the pictures according with your mood.</center>");
	$("a[class=button]").click(function()
	{
		playEffect("#button","bounceOut");
		$('#button').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function()
		{
			playEffect("#button","bounceIn");
			$("#button").html("Please Wait...");
		});
		$.post("./php/auth.php", { op: "url"}, function(xml) 
		{
			token=$(xml).find("token").text();
			tokenSecret=$(xml).find("tokenSecret").text();
			url=$(xml).find("url").text();
			window.location=url;
		});
	});
}

/*
 * Affiche le contenu de la page de d'annonce de la bonne connexion du service avec twitter
 */ 
function linked(name)
{
	$("#content").html("<center><br><br><br><br><br><br><div style=''><img src='./assets/image/Happy.png' style=''><h2><font color='#F5E31F'>Congratulations "+name+" !</font></h2>Your Twitter account is now linked to <font color='#F5E31F'>Tw|irit</font> !<br>You can now use <font color='#F5E31F'>our service</font> by pressing the button below !<br><br><a class='button' id='start'>Start to use Tw|irit</a><br><br>By pressing this button, you will see the setting panel which will allow you to set and upload your pictures.</div></center>");
	$("a[class=button]").click(function()
	{
		window.location.replace ( "http://twirit.fr.nf" );
	});
}

/*
 * Affiche le contenu de la page de d'annonce de la non-connexion du service avec twitter
 */ 
function noLinked()
{
	$("#content").html("<center><br><br><br><br><br><br><div style=''><h2><font color='#F22B2B'>An Error Occurred...</font></h2><br><font color='#F22B2B'>Tw|irit</font> can't connect to your Twitter account...<br>Please <font color='#F22B2B'>retry</font>.<br><br><br><a class='button retry' id='retry'>Retry to link with twitter >></a></div></center>");
	$("a[class*=button]").click(function()
	{
		playEffect("#button","bounceOut");
		$('#button').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function()
		{
			playEffect("#button","bounceIn");
			$("#button").html("Please Wait...");
		});
		$.post("./php/auth.php", { op: "url"}, function(xml) 
		{
			token=$(xml).find("token").text();
			tokenSecret=$(xml).find("tokenSecret").text();
			url=$(xml).find("url").text();
			window.location=url;
		});
	});

}

/*
 * Affiche le panneau d'administration de l'utilisateur
 */
function use(idr)
{
	html="<center><br><h2>Welcome to your <font color='#50BCE3'>administration panel</font> !</h2><p id='info'>With The table below, you can organize, set and upload the <font color='#50BCE3'>pictures</font> corresponding to the <font color='#50BCE3'>#Hashtag</font> of your choice.<br>(Do not forget to set a #normal hashtag, or similar, so you can reset your account image with the original.)</p><br><br><table id='organize'><tr><td class='grid grid-top grid-top-left '>#Hashtag</td> <td class='grid grid-top'>Picture</td> <td class='grid grid-top grid-top-right'>operation</td></tr>";
	
	$.post("./php/use.php", { op: "pictures", id: ""+idr+""}, function(xml) 
	{
		_i=0;
		$(xml).find("picture").each(function(i)
		{
			id=$(this).find("id").text();
			user=$(this).find("user").text();
			tag=$(this).find("tag").text();
			url=$(this).find("url").text();
			_i+=1;
			html=html+"<tr id='"+id+"'><td id='"+id+"tag'class='grid grid-left'>"+tag+"</td><td class='grid grid-middle' style='padding: 0px 0px 0px 0px;'><img style='width:50px;' src='"+url+"'</td><td class='grid grid-middle'><a class='tiny button' id='apply' name='"+id+":"+user+"'>apply</a> <a class='tiny button' id='set' name='"+id+":"+user+"'>set</a> <a class='tiny button retry' style='width:auto;height:20px;line-height:20px;vertical-align:top;' id='delete' name='"+id+":"+user+"'>X</a></td></tr>";
		});
		html=html+"<tr id='end-organize'><td class='grid grid-left grid-bottom-left' id='edit-tag'></td> <td class='grid grid-middle' id='edit-picture'></td> <td class='grid grid-middle grid-bottom-right'><a class='button' name='"+idr+"' id='add' style='color:white;width:95%;'>add new #Hashtag</a></td></tr></table></center>";
		playEffect("#content","flipInX");
		$("#content").html(html);
		if(_i>=7)
		{
			$("#add").hide();
		}
		$("a").click(function()
		{
			i=$(this).attr("name");
			if(i == undefined)
				return;
			d=i.split(":");
			_id=d[0];
			if($(this).attr("id")=="apply") //Application de l'image liée immédiatement
			{
				$.post("./php/use.php", { op: "apply", id: _id}, function(xml) 
				{
					if($(xml).find("result").text()=="ok")
					{
						$("#info").html("<font size=6 color='#38EA38'>Changes made ! =D</font>");
					}
					else
					{
						_code=$(xml).find("code").text();
						if(_code=="0")
							$("#info").html("<font size=6 color='#B72828'>Unable to reach Database... Try later... (err.0).</font>");						
					}					
				});	
			}
			else if($(this).attr("id")=="delete") //Supression de l'image liée et du hashtag associé
			{
				$.post("./php/use.php", { op: "delete", id: _id, user: idr}, function(xml) 
				{
					if($(xml).find("result").text()=="ok")
					{
						playEffect("#content","flipInX");
						use($(xml).find("user").text());
					}
					else
					{
						_code=$(xml).find("code").text();
						if(_code=="0")
							$("#info").html("<font size=6 color='#B72828'>Unable to reach Database... Try later... (err.0).</font>");
						else if(_code=="2")
							$("#info").html("<font size=6 color='#B72828'>Unable to delete the image... Try later... (err.2 )</font>");								
					}
				});				
			}
			else if($(this).attr("id")=="set") //edition du tag associé à l'image
			{
				if($(this).html() != "save")
				{
					$(this).html("save");
					$(this).css("background-color","#38EA38");
					_tag=$("#"+_id+"tag").html();
					$("#"+_id+"tag").html("<input id='change-tag' class='input-preset' value='"+_tag+"'>");
					$("input[class=input-preset]").click(function()
					{
						$(this).removeClass("input-preset");
						$(this).val("");
					});
				}
				else if($(this).html() == "save")
				{
					_tag=$("#change-tag").val();
					if(!_tag.contains('#'))
					{	
						playEffect("#info","bounce");
						$("#info").html("<font size=6 color='#B72828'>Your Hashtag must contains a '#' char.</font>");
						return;
					}
					if(_tag.contains(' ') || _tag.contains('-'))
					{	
						playEffect("#info","bounce");
						$("#info").html("<font size=6 color='#B72828'>You can not put spaces or '-' in your hashtags.</font>");
						return;
					}
					$.post("./php/use.php", { op: "edit", id: _id, user: idr, tag: _tag}, function(xml) 
					{

						if($(xml).find("result").text()=="ok")
						{
							use($(xml).find("user").text());
						}
						else
						{
							_code=$(xml).find("code").text();
							if(_code=="0")
								$("#info").html("<font size=6 color='#B72828'>Unable to reach Database... Try later... (err.0).</font>");
							else if(_code=="1")
							{
								$("#info").html("<font size=6 color='#50BCE3'>The new tag is the same  ! (err.1 )</font>");
								$("#"+_id+"tag").html(_tag);
								$(document).find("a[id*=set]").each(function()
								{
									$(this).html("set");
									$(this).css("background-color","#1281A9");
								});
							}
							else if(_code=="4")
								$("#info").html("<font size=6 color='#50BCE3'>You already have a picture with the same tag. (err.4 )</font>");
						}
					});				
				}

			}
			else if($(this).attr("id")=="add") //ajoute une nouvelle photo associée à un hashtag dans la base de données 
			{
				if($(this).html() != "save")
				{
					$(this).html("save");
					$("#edit-tag").html("<input id='input-tag' class='input-preset' value='enter a #hashtag here...'>");
					playEffect("#input-tag","shake");
					$("#edit-picture").html("<input id='input-picture' type='file'>");
					playEffect("#input-picture","shake");
					$("input[class*=input-preset]").click(function()
					{
						$(this).removeClass("input-preset");
						$(this).val("");
					});
					$("input[type=file]").on("change",function()
					{
					    formdata = new FormData();     
						file = this.files[0];
						if (formdata) 
						{
							formdata.append("upload", file);
							formdata.append("format", "xml");
							$.ajax(
							{
								url: "http://uploads.im/api",
								type: "POST",
								data: formdata,
								processData: false,
								contentType: false,
								success:function(xml)
								{
									if($(xml).find("status_code").text() != "403")
										$("#edit-picture").html("<img id='preview-picture' style='width:50px;' src='"+$(xml).find("img_url").text()+"'>");
									else
									{
										playEffect("#info","bounce");
										$("#info").html("<font size=6 color='#B72828'>Unable to reach image hoster. Please try later.</font>");
										return;
									}
										
								}
							});
						}                      
					}); 
				}
				else
				{
					_user=$(this).attr("name");
					_tag=$("#input-tag").val();
					if(!_tag.contains('#'))
					{	
						playEffect("#info","bounce");
						$("#info").html("<font size=6 color='#B72828'>Your Hashtag must contains a '#' char.</font>");
						return;
					}
					if(_tag.contains(' ') || _tag.contains('-'))
					{	
						playEffect("#info","bounce");
						$("#info").html("<font size=6 color='#B72828'>You can not put spaces or '-' in your hashtags.</font>");
						return;
					}
					if($("#preview-picture").attr("src") == undefined)
					{
						playEffect("#info","bounce");
						$("#info").html("<font size=6 color='#B72828'>You must choose an image from you computer.</font>");
						return;					
					}
					_url=$("#preview-picture").attr("src");
					$.post("./php/use.php", { op: "add", user: _user, tag: _tag, url: _url}, function(xml) 
					{
						if($(xml).find("result").text()=="ok")
						{
							use($(xml).find("user").text());
						}
						else
						{
							_code=$(xml).find("code").text();
							if(_code=="0")
								$("#info").html("<font size=6 color='#B72828'>Unable to reach Database... Try later... (err.0).</font>");
							else if(_code=="3")
								$("#info").html("<font size=6 color='#50BCE3'>We can't add the new entry... Try later... (err.3)</font>");								
						}
					});
				}
			}
		});
	});
	


}