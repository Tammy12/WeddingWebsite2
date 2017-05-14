define(['plugins/dialog','knockout'], function (dialog, ko) {
    var rsvpModal = function () {
        //either 'findGuest' or 'guestResponse'
        this.currentStep = ko.observable('findGuest');
        this.anotherMemberNeg = ko.observable(false);

        this.mainGuestFirstName = ko.observable('');
        this.mainGuestLastName = ko.observable('');
        this.mainGuestId = null;

        this.party = ko.observableArray([]);
        this.extraGuestCount = ko.observableArray([]);
        this.extraGuestCountChoice = ko.observable();
        this.foodOptions = ["Beef", "Fish", "Vegetarian"];

        this.Guest = function (visible, first, last, rehearsal, wedding, choice, other) {
            this.visible = ko.observable(visible);
            this.firstName = ko.observable(first);
            this.lastName = ko.observable(last);
            this.rehearsalAccept = ko.observable(rehearsal);
            this.weddingAccept = ko.observable(wedding);
            this.foodChoice = ko.observable(choice);
            this.foodOther = ko.observable(other);
        }

        this.isActive1 = ko.observable(false);
        this.addActive = function (data, event) {
            data.isActive1(true);
        }
    };

    rsvpModal.prototype.close = function () {
        //dialog.close(this, this.input());
        dialog.close(this);
    };

    rsvpModal.prototype.goBack = function () {
        //dialog.close(this, this.input());
        var self = this;
        self.currentStep('guestResponse');        
    };

    rsvpModal.prototype.validateFirstPage = function() {
        var self = this;
        if(self.mainGuestFirstName() != "" && self.mainGuestFirstName() != null && self.mainGuestLastName() != "" && self.mainGuestLastName() != null) {
            return true;
        }
        else {
            return false;
        }
    };

    rsvpModal.prototype.validateSecondPage = function() {
        var self = this;
        var errorName = false;                              
        var errorRehearsal = false;
        var errorWedding = false;  
        var errorFood = false;     
        //collect any alerts            
        for(var i = 0; i <= self.extraGuestCountChoice(); i++){
            if(i != 0 && (self.party()[i].firstName() == null || self.party()[i].firstName() == "" ||  self.party()[i].lastName() == "" || self.party()[i].lastName() == null)){
                errorName = true;
            }
            if(self.party()[i].rehearsalAccept() == null){
                errorRehearsal = true;
            }
            if(self.party()[i].weddingAccept() == null){
                errorWedding = true;
            }
            if(self.party()[i].weddingAccept() == true && self.party()[i].foodChoice() == null){
                errorFood = true;
            }
        }

        //now show all collected alerts
        var totalAlerts = "";
        if(errorName){
            totalAlerts += "Be sure to fill in first and last names of any guests!\n"
        }
        if(errorRehearsal){
            totalAlerts += "Don't forget to RSVP for the rehearsal dinner!\n"            
        }
        if(errorWedding){
            totalAlerts += "Don't forget to RSVP for the wedding reception!\n"            
        }
        if(errorFood){
            totalAlerts += "Don't forget to select an entree for the wedding reception!\n"            
        }
        
        
        if(!errorName && !errorRehearsal && !errorWedding && !errorFood){
            self.send();
        }
        else alert(totalAlerts);
    };

    rsvpModal.prototype.findGuest = function () {
        //checks guest first/last name against database, get # in party
        var self = this;           
        $.ajax({
            type: "GET",
            url: "search.php",
            data: {firstName: self.mainGuestFirstName(), lastName: self.mainGuestLastName()},
            datatype: 'json',
            contentType: "application/json; charset=utf-8",
            success: function (jsonData) {
                var data = JSON.parse(jsonData);
                if (data.success) {
                    self.mainGuestId = data.id;
                    self.currentStep('guestResponse');
                    for (var i = 0; i < data.partySize; i++) {
                        var guest;
                        self.extraGuestCount.push(i);    
                        if (i == 0) {
                            guest = new self.Guest(true, data.allGuestInfo[i].FirstName, data.allGuestInfo[i].LastName, data.allGuestInfo[i].RehearsalAccept, data.allGuestInfo[i].WeddingAccept, data.allGuestInfo[i].FoodChoice, data.allGuestInfo[i].FoodOther);
                            /*Hacky but must do for now*/
                            guest.rehearsalAccept(data.allGuestInfo[i].RehearsalAccept);
                            guest.weddingAccept(data.allGuestInfo[i].WeddingAccept);
                            guest.foodChoice(data.allGuestInfo[i].FoodChoice);
                            guest.foodOther(data.allGuestInfo[i].FoodOther);

                        }
                        else {
                            if (data.allGuestInfo.length >= i + 1) {
                                guest = new self.Guest(true, data.allGuestInfo[i].FirstName, data.allGuestInfo[i].LastName, data.allGuestInfo[i].RehearsalAccept, data.allGuestInfo[i].WeddingAccept, data.allGuestInfo[i].FoodChoice, data.allGuestInfo[i].FoodOther);
                                guest.rehearsalAccept(data.allGuestInfo[i].RehearsalAccept);
                                guest.weddingAccept(data.allGuestInfo[i].WeddingAccept);
                                guest.foodChoice(data.allGuestInfo[i].FoodChoice);
                                guest.foodOther(data.allGuestInfo[i].FoodOther);
                            }
                            else {
                                guest = new self.Guest(false, "", "");
                                /*Hacky but must do for now*/
                                guest.rehearsalAccept(null);
                                guest.weddingAccept(null);
                                guest.foodChoice("");
                                guest.foodOther("");
                            }                                                      
                        }
                        self.party.push(guest);
                    }
                    self.extraGuestCountChoice(data.extraGuestChoice);
                }
                else {
                    debugger;
                    //toast.error(data.message);
                    //NEED TO TELL GUEST THAT THEY WEREN'T FOUND IN DB!
                    alert(data.message);
                }
            },
            error: function (data) {
                debugger;
            }

        });
    };

    rsvpModal.prototype.send = function () {
        //checks guest first/last name against database, get # in party
        var self = this;
        var sendData = [];
        //var sendData = [{"FoodChoice": "bat"},{"FoodChoice": "egg"}];
        
        for (var i = 0; i <= self.extraGuestCountChoice(); i++) {
            var data = {
                "FirstName": self.party()[i].firstName(),
                "LastName": self.party()[i].lastName(),
                "RehearsalAccept": self.party()[i].rehearsalAccept(),
                "WeddingAccept": self.party()[i].weddingAccept(),
                "FoodChoice": self.party()[i].foodChoice(),
                "FoodOther": self.party()[i].foodOther()
            };
            sendData.push(data);
        }
        $.ajax({
            type: "GET",
            url: "sendRSVP.php",
            data: { "mainGuestId": self.mainGuestId, "extraGuestChoice": self.extraGuestCountChoice(), "sendData": sendData },//stringify doesn't work with php
            datatype: 'json',
            contentType: "application/json; charset=utf-8",
            success: function (jsonData) {
                var data = JSON.parse(jsonData);
                if (data.success) {
                    self.currentStep('confirmation');
                }
                else {
                    //toast.error(data.message);
                    debugger;
                }
            },
            error: function (data) {
                debugger;
            }

        });

        //dialog.close(this);
    };


    rsvpModal.show = function () {
        return dialog.show(new rsvpModal());
    };


    return rsvpModal;
});
