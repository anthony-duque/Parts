var app = angular.module("DefineShopStagesApp", []);

var defineShopStagesCtrlr = function($scope, $http, $window) {

    $scope.companyID = $window.sessionStorage.getItem('companyID');

    $scope.stageSelected = null;

    Get_Shop_List($scope.companyID);   // Get all the available Shops for this company

    Get_Company_Stages($scope.companyID);

    Get_Stage_Headings();   // Get all the available Stage Heading values

///////////////////////////////////////////

    function Get_Company_Stages(company_id)
    {
        $http.get('../../php/Company_Stage_Headings.php?companyID=' + company_id)
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

                // remove any stage headings that are already assigned to the company
            $scope.stageHeadings = $scope.stageHeadings.filter(heading => !$scope.companyStages.some(stage => stage.heading_id === heading.id));

            }
    }   // function StageHeadingsSuccess()


    function StageHeadingFailure(response)
    {
        console.log("Fetching Stage Headings failed.");
    }


    function Get_Shop_List(company_id)
    {
        $http.get('../../php/Shops.php?companyID=' + company_id)
            .then(ShopListSuccess)
            .catch(ShopListFailure);

    }   // function Get_Shop_List()


    function ShopListSuccess(response)
    {
        if (response.data)
        {
            console.log("Shop List fetched successfully!");
            console.log(response.data);
             $scope.shopList = response.data;
        }
    }   // function ShopListSuccess()


    function ShopListFailure(response)
    {
        console.log("Fetching Shop List failed.");
    }

    $scope.addHeadingToCompany = function(stage){

            // add the heading to the company
        $scope.companyStages.push(stage);

            // remove it from the stage heading list
        $scope.stageHeadings = $scope.stageHeadings.filter(heading => heading.heading_id !== stage.heading_id);
    }


    $scope.removeCompanyHeading = function(stage){

        console.log(stage);
        $scope.stageHeadings.push(stage);

            // remove it from the stage heading list
        $scope.companyStages = $scope.companyStages.filter(heading => heading.heading_id !== stage.heading_id);
    }


    $scope.selectStage = function(stage){

        if (!$scope.stageSelected){
            $scope.stageSelected = stage;
        } else {
            $scope.stageSelected = null;
        }
    }


        // move up the selected stage in the list
    $scope.moveStageUp = function(selectedID){

        const index = $scope.companyStages.findIndex(stage => stage.heading_id === selectedID);
        
        if (index == 0){
            
            return;     // if there is only one stage do nothing

        } else {

            $scope.companyStages[index] = $scope.companyStages[index - 1];
            $scope.companyStages[index - 1] = $scope.stageSelected;
        }
    }


        // move down the selected stage in the list
    $scope.moveStageDown = function(selectedID){

        const index = $scope.companyStages.findIndex(stage => stage.heading_id === selectedID);
        
        if (index == ($scope.companyStages.length - 1)){
            
            return;     // if there is only one stage do nothing

        } else {

            $scope.companyStages[index] = $scope.companyStages[index + 1];
            $scope.companyStages[index + 1] = $scope.stageSelected;
        }
    }


    $scope.Update_Stages = function()
    {
//        console.log(JSON.stringify($scope.companyStages));
        $http.post('../../php/Company_Stage_Headings.php?companyID=' + $scope.companyID, JSON.stringify($scope.companyStages))
            .then(function(response) {
                console.log("Company stages updated successfully!");
                console.log(response.data);
            })
            .catch(function(error) {
                console.error("Error updating company stages:", error);
            });
    }   // $scope.Update_Stages()

}   // defineShopStagesCtrlr()

app.controller("DefineShopStagesCtrlr", defineShopStagesCtrlr);