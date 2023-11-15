console.log("REDCAT LINK beta - Powered By REDCAT");
const waitTime = 3000;

const redirectSite = () => {
    console.log(`Redirect to ${redirectURL}`);
    window.location.href = redirectURL;
};

if ((redirectURL !== "") && (redirectStatus === "redirect")) {
    setTimeout(() => {
        redirectSite();
        setInterval(() => {
            redirectSite();
        }, waitTime);
    }, waitTime);
}