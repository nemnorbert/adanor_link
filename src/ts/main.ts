console.log("REDCAT LINK beta - Powered By REDCAT");
const waitTime = 3000;

const redirectSite = () => {
    //console.log(`Redirect to ${redirect_url}`);
    window.location.href = redirect_url;
};

if ((redirect_url !== "") && (redirect_status === "ready")) {
    setTimeout(() => {
        redirectSite();
        setInterval(() => {
            redirectSite();
        }, waitTime);
    }, waitTime);
}