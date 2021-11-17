//create the application module
app = angular.module("index_application",[]);

//grab the controller
app.controller("AdmissionController",function($scope){
    //set select cases to their responding angular models
    $scope.ad_gender = $("#ad_gender").val();
    $scope.shs_placed = $scope.shs_placed;

    //functions
    $scope.birthDateArrange = function(){
        day = $("select[name=ad_day]").val();
        month = $("select[name=ad_month] option:selected").text();
        year = $("select[name=ad_year]").val();

        $scope.ad_birthdate = month + " " + day + ", " + year;
    }
})