define(['plugins/http', 'durandal/app', 'knockout', './rsvp'], function (http, app, ko, rsvp) {
    //Note: This module exports an object.
    //That means that every module that "requires" it will get the same object instance.
    //If you wish to be able to create multiple instances, instead export a function.
    //See the "welcome" module for an example of function export.
    var vm = {
        hotels: [],
        newHotel: function(name, street, city, phone, code, cost, url){
            this.name = name;
            this.street = street;
            this.city = city;
            this.phone = phone;
            this.code = code;
            this.cost = cost;
            this.url = url;
        },
        showRsvpModal: function() {
            rsvp.show();
        },
        activate: function () {
            var self = this;
            self.hotels = [];
            self.hotels.push(new self.newHotel("The Homestead*", "1625 Hinman Ave", "Evanston, IL 60201", "847-475-3300", "Use online rate code 'devere'", "120", "https://thehomestead.net/"));
            self.hotels.push(new self.newHotel("Hilton Orrington*", "1710 Orrington Ave", "Evanston, IL 60201", "847-866-8700", "Use online rate code 'XDW'", "185", "http://www3.hilton.com/en/hotels/illinois/hilton-orrington-evanston-ORDOEHF/index.html"));
            self.hotels.push(new self.newHotel("Hyatt House Evanston", "1515 Chicago Ave", "Evanston, IL 60201", "847-864-2300", "Please reference the DeVere/Xu wedding", "189", "https://chicagoevanston.house.hyatt.com/en/hotel/home.html"));
        }
    }
    return vm;
});