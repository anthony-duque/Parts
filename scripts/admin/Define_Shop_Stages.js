var app = angular.module("DefineShopStagesApp", []);

var defineShopStagesCtrlr = function($scope, $http){

    $scope.companyID = $window.sessionStorage.getItem('companyID');
    //$scope.companyID = 2;

        //  Pull from shops table
    $scope.shops = [
        { shop_id: 1, name: "World Class Collision I"},
        { shop_id: 2, name: "World Class Collision II"},
    ];


        // Pull from company_stages table
//    $scope.companyStages = [];

    $scope.stageSelected = null;

    Get_Stage_Headings();   // Get all the available Stage Heading values

    Get_Company_Stages($scope.companyID);

///////////////////////////////////////////

    function Get_Company_Stages(company_id)
    {
        $http.get('../../php/Company_Stages.php?companyID=' + company_id)
            .then(CompanyStagesSuccess)
            .catch(CompanyStagesFailure);

    }   // function Get_Company_Stages()


    function CompanyStagesSuccess(response)
    {
        if (response.data)
        {
            console.log("Company Stage Headings fetched successfully!");
            console.log(response.data);
            $scope.companyStages = response.data;
        }
    }   // function StageHeadingsSuccess()


    function CompanyStagesFailure(response)
    {
        console.log("Fetching Company Stage Headings failed.");
    }


    function Get_Stage_Headings()
    {
        $http.get('../../php/Stage_Headings.php')
            .then(StageHeadingSuccess)
            .catch(StageHeadingFailure);

    }   // function Get_Stage_Headings()


    function StageHeadingSuccess(response)
    {
        if (response.data)
        {
            console.log("Stage Headings fetched successfully!");
            console.log(response.data);
             $scope.stageHeadings = response.data;
        }
    }   // function StageHeadingsSuccess()


    function StageHeadingFailure(response)
    {
        console.log("Fetching Stage Headings failed.");
    }

    $scope.addHeadingToCompany = function(stage){

            // add the heading to the company
        $scope.companyStages.push(stage);

            // remove it from the stage heading list
        $scope.stageHeadings = $scope.stageHeadings.filter(heading => heading.id !== stage.id);
    };


    $scope.removeCompanyHeading = function(stage){

        $scope.stageHeadings.push(stage);

            // remove it from the stage heading list
        $scope.companyStages = $scope.companyStages.filter(heading => heading.id !== stage.id);
    };


    $scope.selectStage = function(stage){

        if (!$scope.stageSelected){
            $scope.stageSelected = stage;
        } else {
            $scope.stageSelected = null;
        }
    };


        // move up the selected stage in the list
    $scope.moveStageUp = function(selectedID){

        const index = $scope.companyStages.findIndex(stage => stage.id === selectedID);
        
        if (index == 0){
            
            return;     // if there is only one stage do nothing

        } else {

            $scope.companyStages[index] = $scope.companyStages[index - 1];
            $scope.companyStages[index - 1] = $scope.stageSelected;
        }
    };


        // move down the selected stage in the list
    $scope.moveStageDown = function(selectedID){

        const index = $scope.companyStages.findIndex(stage => stage.id === selectedID);
        
        if (index == ($scope.companyStages.length - 1)){
            
            return;     // if there is only one stage do nothing

        } else {

            $scope.companyStages[index] = $scope.companyStages[index + 1];
            $scope.companyStages[index + 1] = $scope.stageSelected;
        }
    };


    $scope.Update_Stages = function()
    {

    }

}   // defineShopStagesCtrlr()

app.controller("DefineShopStagesCtrlr", defineShopStagesCtrlr);