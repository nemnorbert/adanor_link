console.log("REDCAT LINK beta - Powered By REDCAT");

async function redirectSite() {
    while (true) {
        await new Promise(resolve => setTimeout(resolve, 3000));
        window.location.href = redirectURL;
        console.log("Refresh page");
    }
}

if (redirectURL != "") {
    redirectSite();
} else {
    console.log("Error 404")
}