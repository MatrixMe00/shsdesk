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

    $scope.formreset = function(){
        //cssps details
        $scope.ad_enrol_code = ""

        //personal details of candidate
        $scope.ad_jhs = ""
        $scope.ad_jhs_town = ""
        $scope.ad_jhs_district = ""
        $scope.ad_birthdate = ""
        $scope.ad_year = ""
        $scope.ad_month = ""
        $scope.ad_day = ""
        $scope.ad_birth_place = ""

        //parents particulars
        $scope.ad_father_name = ""
        $scope.ad_father_occupation = ""
        $scope.ad_mother_name = ""
        $scope.ad_mother_occupation = ""
        $scope.ad_guardian_name = ""
        $scope.ad_resident = ""
        $scope.ad_postal_address = ""
        $scope.ad_phone = ""
        $scope.ad_other_phone = ""

        //others
        $scope.interest = ""
        $scope.ad_awards = ""
        $scope.ad_position = ""

        //witness
        $scope.ad_witness = ""
        $scope.ad_witness_phone = ""
    }
})