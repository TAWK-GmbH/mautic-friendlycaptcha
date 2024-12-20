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
}

function checkPresenceOfFriendlyChallenge(version) {
    return 'v1' == version && friendlyChallenge || 'v2' == version && frcaptcha;
}

/**
 * 
 * @param {string} wrapperId Friendly Captcha form wrapper id
 * @param {string} inputName Name of the friendly captcha input
 * @param {string} siteKey Friendly Captcha site key
 * @param {string} siteKey Friendly Captcha site key
 * @param {string} version Friendly Captcha api version
 */
function scheduleAddCaptcha(wrapperId, inputName, siteKey, version, mode) {
    const wrapper = document.querySelector(`#${wrapperId}`);
    if (!wrapper) {
        throw new Error(`No friendly captcha wrapper element found with id '${wrapperId}'`);
    }

    if (checkPresenceOfFriendlyChallenge(version)) {
        addCaptcha(wrapper, inputName, siteKey, version, mode)
        return;
    }

    const timer = setInterval(function () {
        if (checkPresenceOfFriendlyChallenge(version)) {
            addCaptcha(wrapper, inputName, siteKey, version, mode);
            clearInterval(timer);
        }    
        
    }, 5000);
};