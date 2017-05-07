define(['plugins/http', 'durandal/app', 'knockout', './rsvp'], function (http, app, ko, rsvp) {
    //Note: This module exports an object.
    //That means that every module that "requires" it will get the same object instance.
    //If you wish to be able to create multiple instances, instead export a function.
    //See the "welcome" module for an example of function export.
    var vm = {
        hotels: [],
        newHotel: function(name, street, city, phone, cost, url){
            this.name = name;
            this.street = street;
            this.city = city;
            this.phone = phone;
            this.cost = cost;
            this.url = url;
        },
        showRsvpModal: function() {
            rsvp.show();
        },
        activate: function () {
            var self = this;
            self.hotels = [];
            self.hotels.push(new self.newHotel("The Homestead", "1625 Hinman Ave", "Evanston, IL 60201", "847-475-3300", "120", "https://thehomestead.net/"));
            self.hotels.push(new self.newHotel("Hilton Orrington", "1710 Orrington Ave", "Evanston, IL 60201", "847-866-8700", "200", "http://www3.hilton.com/en/hotels/illinois/hilton-orrington-evanston-ORDOEHF/index.html"));
        }
    }
    return vm;
});