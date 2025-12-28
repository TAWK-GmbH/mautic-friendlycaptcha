(function () {
    if (window.FriendlyCaptchaMautic) {
        return;
    }

    function getWrapperElement(wrapperId) {
        const wrapper = document.querySelector(`#${wrapperId}`);
        if (!wrapper) {
            throw new Error(`No friendly captcha wrapper element found with id '${wrapperId}'`);
        }
        return wrapper;
    }

    function addCaptcha(wrapper, inputName, siteKey, version, mode) {
        if (version == 'v1') {
            const myCustomWidget = new friendlyChallenge.WidgetInstance(wrapper, {
                sitekey: siteKey,
                startMode: mode,
                solutionFieldName: inputName
            });

            return;
        } 

        const myCustomWidget = frcaptcha.createWidget({
            element: wrapper,
            sitekey: siteKey,
            startMode: mode,
            formFieldName: inputName
        });

        console.debug(`Captcha ${inputName} added to the form.`);
    }

    function checkPresenceOfFriendlyChallenge(version) {
        return 'v1' == version && typeof friendlyChallenge !== "undefined" || 'v2' == version && typeof frcaptcha !== "undefined";
    }

    /**
     * @param {string} wrapperId Friendly Captcha form wrapper id
     * @param {string} inputName Name of the friendly captcha input
     * @param {string} siteKey Friendly Captcha site key
     * @param {string} siteKey Friendly Captcha site key
     * @param {string} version Friendly Captcha api version
     */
    function scheduleAddCaptcha(wrapperId, inputName, siteKey, version, mode) {
        const wrapper = getWrapperElement(wrapperId);

        if (checkPresenceOfFriendlyChallenge(version)) {
            addCaptcha(wrapper, inputName, siteKey, version, mode)
            return;
        }

        console.debug('Waiting for Friendly Captcha library to load...');

        const timer = setInterval(function () {
            if (checkPresenceOfFriendlyChallenge(version)) {
                addCaptcha(wrapper, inputName, siteKey, version, mode);
                clearInterval(timer);
            } else {
                console.debug('Waiting for Friendly Captcha library to load...');
            }
        }, 500);
    };

    function displayCaptchasInForms(queue) {
        for (const captchaData of queue) {
            addCaptcha(getWrapperElement(captchaData.wrapperId), captchaData.inputName, captchaData.siteKey, captchaData.version, captchaData.mode);
        }
    }

    window.FriendlyCaptchaMautic = {
        scheduleAddCaptcha,
        displayCaptchasInForms,
    };
})();