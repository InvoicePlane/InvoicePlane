var footerCookie = document.querySelector("#footer-cookie");
var footerCookieAccept = document.querySelector("#accept");

if (document.cookie.indexOf("dakitecCookieBanner=") == -1) {
	footerCookie.style.display = "block";
};

footerCookieAccept.onclick = function(e) {
	var cookieDate = new Date();
	//milliseconds
	//30 Tage = 2592000000
	//360 Tage = 31104000000
    cookieDate.setTime(new Date().getTime() + 2592000000);
    document.cookie = "dakitecCookieBanner = 1; path=/; secure; expires=" + cookieDate.toUTCString();
    footerCookie.style.display = "none";
};
