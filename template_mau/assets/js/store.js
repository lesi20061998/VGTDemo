document.addEventListener("alpine:init", () => {
    Alpine.store("appStore", {
        dir: sessionStorage.getItem("dir") || "ltr",

        toggleDir() {
            console.log("toggleDir", this.dir);
            this.dir = this.dir === "ltr" ? "rtl" : "ltr";
            sessionStorage.setItem("dir", this.dir);

            this.handleUrl()
        },
        handleUrl() {
            var bootstrapLinkEle = document.getElementById("bootstrap-style"); 
            var appLinkEle = document.getElementById("app-style"); 

            if(this.dir === "rtl") {
                bootstrapLinkEle.setAttribute("href", "assets/css/bootstrap-rtl.min.css");
                appLinkEle.setAttribute("href", "assets/css/app-rtl.min.css");
            } else {
                bootstrapLinkEle.setAttribute("href", "assets/css/bootstrap.min.css");
                appLinkEle.setAttribute("href", "assets/css/app.min.css");
            }
        }
    })

    Alpine.store("appStore").handleUrl();
})