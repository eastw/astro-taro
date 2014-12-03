$("#menuV li").each(function () {
	if($("ul", this).length){// если LI имеет дочерний UL
		$(this).addClass("jdm_active");
	}
});
var jshover = function() {
	var sfEls = $("#menuV li");
	for (var i=0; i<sfEls.length; i++){
		sfEls[i].onmouseover=function(){
			this.className+=" jshover";
		}
		sfEls[i].onmouseout=function(){
			this.className=this.className.replace(new RegExp(" jshover\\b"), "");
		}
	}
}
if(window.attachEvent){window.attachEvent("onload", jshover);}