// CLASS
class RedirectIt {
    private baseURL: string;
    private status: string;
    private waitTime: number;

    constructor(baseURL:string, status: string, waitTime = 3000) {
        this.baseURL = baseURL;
        this.status = status;
        this.waitTime = waitTime;
    }

    redirectSite() {
        //console.log(this.baseURL);
        window.location.href = this.baseURL;
    }

    initRedirect() {
        if (this.baseURL !== "" && this.status === "ready") {
            setTimeout(() => {
                this.redirectSite();
                setInterval(() => {
                    this.redirectSite();
                }, this.waitTime);
            }, this.waitTime);
        }
    }
}

// BASE
const redirectIt = new RedirectIt(redirect_url, redirect_status);
redirectIt.initRedirect();