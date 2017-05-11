define(['plugins/router', 'durandal/app','knockout'], function (router, app, ko) {
    return {
        router: router,
        search: function() {
            //It's really easy to show a message box.
            //You can add custom options too. Also, it returns a promise for the user's response.
            app.showMessage('Search not yet implemented...');
        },
        activate: function () {
            router.map([
                { route: '', title:'Welcome', moduleId: 'viewmodels/welcome', nav: true },
                { route: 'details', title: 'RSVP', moduleId: 'viewmodels/details', nav: true },
                { route: 'registry', moduleId: 'viewmodels/registry', nav: true }

            ]).buildNavigationModel();
            
            return router.activate();
        }
    };
});