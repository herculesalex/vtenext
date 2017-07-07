function Viewer(c){function O(){var a,b,C,d,f;c&&(C=c.getPluginName(),d=c.getPluginVersion(),f=c.getPluginURL());a=document.createElement("div");a.id="aboutDialogCentererTable";b=document.createElement("div");b.id="aboutDialogCentererCell";q=document.createElement("div");q.id="aboutDialog";q.innerHTML='<h1>ViewerJS</h1><p>Open Source document viewer for webpages, built with HTML and JavaScript.</p><p>Learn more and get your own copy on the <a href="http://viewerjs.org/" target="_blank">ViewerJS website</a>.</p>'+(c?'<p>Using the <a href = "'+f+'" target="_blank">'+C+'</a> (<span id = "pluginVersion">'+d+"</span>) plugin to show you this document.</p>":"")+'<p>Supported by <a href="http://nlnet.nl" target="_blank"><br><img src="modules/Messages/src/ViewerJS/images/nlnet.png" width="160" height="60" alt="NLnet Foundation"></a></p><p>Made by <a href="http://kogmbh.com" target="_blank"><br><img src="modules/Messages/src/ViewerJS/images/kogmbh.png" width="172" height="40" alt="KO GmbH"></a></p><button id = "aboutDialogCloseButton" class = "toolbarButton textButton">Close</button>';u.appendChild(a);a.appendChild(b);b.appendChild(q);a=document.createElement("button");a.id="about";a.className="toolbarButton textButton about";a.title="About";a.innerHTML="ViewerJS";a.style.display='none';P.appendChild(a);a.addEventListener("click",function(){u.style.display="block"});document.getElementById("aboutDialogCloseButton").addEventListener("click",function(){u.style.display="none"})}
function D(a){var b=Q.options,c,d=!1,f;for(f=0;f<b.length;f+=1)c=b[f],c.value!==a?c.selected=!1:d=c.selected=!0;return d}
function E(a,c,d){a!==b.getZoomLevel()&&(b.setZoomLevel(a),d=document.createEvent("UIEvents"),d.initUIEvent("scalechange",!1,!1,window,0),d.scale=a,d.resetAutoSettings=c,window.dispatchEvent(d))}
function F(){var a;if(c.onScroll)c.onScroll();c.getPageInView&&(a=c.getPageInView())&&(l=a,document.getElementById("pageNumber").value=a)}
function G(a){window.clearTimeout(H);H=window.setTimeout(function(){F()},a)}
function e(a,b,g){var e,f;if(e="custom"===a?parseFloat(document.getElementById("customScaleOption").textContent)/ 100:parseFloat(a))E(e,!0,g);else{e=d.clientWidth-r;f=d.clientHeight-r;switch(a){case"page-actual":E(1,b,g);break;case"page-width":c.fitToWidth(e);break;case"page-height":c.fitToHeight(f);break;case"page-fit":c.fitToPage(e,f);break;case"auto":c.isSlideshow()?c.fitToPage(e+r,f+r):c.fitSmart(e)}
D(a)}
G(300)}
function s(){m=!m;h&&!m&&b.togglePresentationMode()}
function v(){t&&(w.className="viewer-touched",window.clearTimeout(I),I=window.setTimeout(function(){w.className=""},5E3))}
function x(){k.classList.add("viewer-touched");n.classList.add("viewer-touched");window.clearTimeout(J);J=window.setTimeout(function(){y()},5E3)}
function y(){k.classList.remove("viewer-touched");n.classList.remove("viewer-touched")}
function z(){k.classList.contains("viewer-touched")?y():x()}
function K(a){blanked.style.display="block";blanked.style.backgroundColor=a;y()}
var b=this,r=40,h=!1,m=!1,L=!1,t=!1,A,g=document.getElementById("viewer"),d=document.getElementById("canvasContainer"),w=document.getElementById("overlayNavigator"),k=document.getElementById("titlebar"),n=document.getElementById("toolbarContainer"),M=document.getElementById("toolbarLeft"),R=document.getElementById("toolbarMiddleContainer"),Q=document.getElementById("scaleSelect"),u=document.getElementById("dialogOverlay"),P=document.getElementById("toolbarRight"),q,N,p=[],l,H,I,J;this.initialize=function(){var a=String(document.location),B=a.indexOf("#"),a=a.substr(B+1);-1===B||0===a.length?console.log("Could not parse file path argument."):(A=a,N=A.replace(/^.*[\\\/]/,""),document.title=N,document.getElementById("documentName").innerHTML=document.title,c.onLoad=function(){document.getElementById("pluginVersion").innerHTML=c.getPluginVersion();(t=c.isSlideshow())?(d.classList.add("slideshow"),M.style.visibility="visible"):(R.style.visibility="visible",c.getPageInView&&(M.style.visibility="visible"));L=!0;p=c.getPages();document.getElementById("numPages").innerHTML="of "+p.length;b.showPage(1);e("auto");d.onscroll=F;G()},c.initialize(d,a))};this.showPage=function(a){0>=a?a=1:a>p.length&&(a=p.length);c.showPage(a);l=a;document.getElementById("pageNumber").value=l};this.showNextPage=function(){b.showPage(l+1)};this.showPreviousPage=function(){b.showPage(l-1)};this.download=function(){var a=A.split("#")[0];window.open(a+"#viewer.action=download","_parent")};this.toggleFullScreen=function(){m?document.exitFullscreen?document.exitFullscreen():document.cancelFullScreen?document.cancelFullScreen():document.mozCancelFullScreen?document.mozCancelFullScreen():document.webkitExitFullscreen?document.webkitExitFullscreen():document.webkitCancelFullScreen?document.webkitCancelFullScreen():document.msExitFullscreen&&document.msExitFullscreen():g.requestFullscreen?g.requestFullscreen():g.mozRequestFullScreen?g.mozRequestFullScreen():g.webkitRequestFullscreen?g.webkitRequestFullscreen():g.webkitRequestFullScreen?g.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT):g.msRequestFullscreen&&g.msRequestFullscreen()};this.togglePresentationMode=function(){var a=document.getElementById("overlayCloseButton");h?("block"===blanked.style.display&&(blanked.style.display="none",z()),k.style.display=n.style.display="block",a.style.display="none",d.classList.remove("presentationMode"),d.onmouseup=function(){},d.oncontextmenu=function(){},d.onmousedown=function(){},e("auto"),t=c.isSlideshow()):(k.style.display=n.style.display="none",a.style.display="block",d.classList.add("presentationMode"),t=!0,d.onmousedown=function(a){a.preventDefault()},d.oncontextmenu=function(a){a.preventDefault()},d.onmouseup=function(a){a.preventDefault();1===a.which?b.showNextPage():b.showPreviousPage()},e("page-fit"));h=!h};this.getZoomLevel=function(){return c.getZoomLevel()};this.setZoomLevel=function(a){c.setZoomLevel(a)};this.zoomOut=function(){var a=(b.getZoomLevel()/ 1.1).toFixed(2),a=Math.max(0.25,a);e(a,!0)};this.zoomIn=function(){var a=(1.1*b.getZoomLevel()).toFixed(2),a=Math.min(4,a);e(a,!0)};(function(){O();c&&(b.initialize(),document.exitFullscreen||document.cancelFullScreen||document.mozCancelFullScreen||document.webkitExitFullscreen||document.webkitCancelFullScreen||document.msExitFullscreen||(document.getElementById("fullscreen").style.visibility="hidden",document.getElementById("presentation").style.visibility="hidden"),document.getElementById("overlayCloseButton").addEventListener("click",b.toggleFullScreen),document.getElementById("fullscreen").addEventListener("click",b.toggleFullScreen),document.getElementById("presentation").addEventListener("click",function(){m||b.toggleFullScreen();b.togglePresentationMode()}),document.addEventListener("fullscreenchange",s),document.addEventListener("webkitfullscreenchange",s),document.addEventListener("mozfullscreenchange",s),document.addEventListener("MSFullscreenChange",s),document.getElementById("download").addEventListener("click",function(){b.download()}),document.getElementById("zoomOut").addEventListener("click",function(){b.zoomOut()}),document.getElementById("zoomIn").addEventListener("click",function(){b.zoomIn()}),document.getElementById("previous").addEventListener("click",function(){b.showPreviousPage()}),document.getElementById("next").addEventListener("click",function(){b.showNextPage()}),document.getElementById("previousPage").addEventListener("click",function(){b.showPreviousPage()}),document.getElementById("nextPage").addEventListener("click",function(){b.showNextPage()}),document.getElementById("pageNumber").addEventListener("change",function(){b.showPage(this.value)}),document.getElementById("scaleSelect").addEventListener("change",function(){e(this.value)}),d.addEventListener("click",v),w.addEventListener("click",v),d.addEventListener("click",z),k.addEventListener("click",x),n.addEventListener("click",x),window.addEventListener("scalechange",function(a){var b=document.getElementById("customScaleOption"),c=D(String(a.scale));b.selected=!1;c||(b.textContent=Math.round(1E4*a.scale)/ 100+"%",b.selected=!0)},!0),window.addEventListener("resize",function(a){L&&(document.getElementById("pageWidthOption").selected||document.getElementById("pageAutoOption").selected)&&e(document.getElementById("scaleSelect").value);v()}),window.addEventListener("keydown",function(a){var c=a.keyCode;a=a.shiftKey;if("block"===blanked.style.display)switch(c){case 16:case 17:case 18:case 91:case 93:case 224:case 225:break;default:blanked.style.display="none",z()}else switch(c){case 8:case 33:case 37:case 38:case 80:b.showPreviousPage();break;case 13:case 34:case 39:case 40:case 78:b.showNextPage();break;case 32:a?b.showPreviousPage():b.showNextPage();break;case 66:case 190:h&&K("#000");break;case 87:case 188:h&&K("#FFF");break;case 36:b.showPage(0);break;case 35:b.showPage(p.length)}}))})()};