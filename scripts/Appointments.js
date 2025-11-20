var app = angular.module("AppointmentsApp", []);

var apptCtrlr = function($scope){

   $scope.timeSlots = [
      "8:30 am",
      "9:00 am",
      "9:30 am",
      "10:00 am",
      "10:30 am",
      "11:00 am",
      "11:30 am",
      "12:00 pm",
      "12:30 pm",
      "1:00 pm",
      "1:30 pm",
      "2:00 pm",
      "2:30 pm",
      "3:00 pm",
      "3:30 pm",
      "4:00 pm",
      "4:30 pm"
   ];

   class Appointment {

      constructor(sched){
         this.schedule  = sched;
         this.owner     = '';
         this.vehicle   = '';
         this.insurance = '';
         this.RO        = null;
      }  // constructor()

   }  // Appointment{}


   class Slot{

   }  // Slot{}

   $scope.appointments = [];   // [week] [appt] = { day, slot, reason, owner, vehicle, insurance, ro }

// Test Appointments
   $scope.appointments.push("Wed Oct 15 2025 12:00 pm");
/////////////////////////

   $scope.firstWeek = [];
   $scope.secondWeek = [];

   $scope.appt_day = '';
   $scope.appt_slot = '';

   const NUM_DAYS = 7;

   var today = new Date();
   var newDate = new Date(today);

   for (var i=0; i < NUM_DAYS; ++i){

      newDate.setDate(today.getDate() + i);

//      $scope.timeSlots.forEach((eachSlot, index)=>{
      //         $scope.appointments[newDate][eachSlot] =
      //         $scope.appointments[index] = new Appointment(newDate + " " + eachSlot);
//      });

      $scope.firstWeek[i] = newDate.toDateString();

//      newDate.setDate(today.getDate() + i + NUM_DAYS);
//      $scope.secondWeek[i] = newDate.toDateString();

   }  // for (let i=...)
/*

   for (let i=0; i < NUM_DAYS; ++i){
     newDate.setDate(today.getDate() + i);
     $scope.appointments[0][i] = new Appointment(newDate.toDateString());
  }
  */

  newDate = new Date();

  for (var j=0; j < NUM_DAYS; ++j){

     newDate.setDate(today.getDate() + j + NUM_DAYS);

//     $scope.timeSlots.forEach((eachSlot, index)=>{
//        $scope.appointments[index + NUM_DAYS] = new Appointment(newDate.toDateString() + " " + eachSlot);
//     });

     $scope.secondWeek[j] = newDate.toDateString();
  }   // for (let i=...)


   $scope.screenWidth   = window.screen.availWidth;
   $scope.screenHeight  = window.screen.availHeight;

   $scope.slotClicked   = false;

   $scope.clicked = function(apptDay, apptSlot){
      $scope.appt_day   = apptDay;
      $scope.appt_slot  = apptSlot;
      $scope.slotClicked = true;
   }  // clicked()


   $scope.slotFound = function(sched){

      var bgClass = '';

      if ($scope.appointments.includes(sched) == true){
         bgClass = 'red';
      }

      return bgClass;
   }     // slotFound()


}   // apptCtrlr()

app.controller("AppointmentsCtrlr", apptCtrlr);
