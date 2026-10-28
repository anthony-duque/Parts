var app = angular.module("DefineShopStagesApp", []);

var defineShopStagesCtrlr = function($scope, $http, $window) {

    $scope.companyID = $window.sessionStorage.getItem('companyID');

    $scope.stageSelected = null;
    $scope.shopStages = [];
    $scope.stageHeadings = [];

    Get_Shop_List($scope.companyID);   // Get all the available Shops for this company

//    Get_Shop_Stages($scope.companyID);

    Get_Stage_Headings();   // Get all the available Stage Heading values

///////////////////////////////////////////

    function Get_Shop_Stages(shop_id)
    {
        $http.get('../../php/Shop_Stage_Headings.php?shopID=' + shop_id)
            .then(ShopStagesSuccess)
            .catch(ShopStagesFailure);

    }   // function Get_Shop_Stages()


    function ShopStagesSuccess(response)
    {
        if (response.data)
        {
            console.log("Shop Stage Headings fetched successfully!");
            console.log(response.data);
            $scope.shopStages = response.data;
        }
    }   // function ShopStagesSuccess()


    function ShopStagesFailure(response)
    {
        console.log("Fetching Shop Stage Headings failed.");
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
            $scope.stageHeadings = $scope.stageHeadings.filter(heading => !$scope.shopStages.some(stage => stage.heading_code === heading.code));

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
        $scope.shopStages.push(stage);

            // remove it from the stage heading list
        $scope.stageHeadings = $scope.stageHeadings.filter(heading => heading.code !== stage.code);
    }


    $scope.removeCompanyHeading = function(stage){

        console.log(stage);
        $scope.stageHeadings.push(stage);

            // remove it from the stage heading list
        $scope.shopStages = $scope.shopStages.filter(heading => heading.code !== stage.code);
    }


    $scope.selectStage = function(stage){

        if (!$scope.stageSelected){
            $scope.stageSelected = stage;
        } else {
            $scope.stageSelected = null;
        }
    }


        // move up the selected stage in the list
    $scope.moveStageUp = function(stageCode){

        const index = $scope.shopStages.findIndex(stage => stage.code === stageCode);
        
        if (index == 0){
            
            return;     // if there is only one stage do nothing

        } else {

            $scope.shopStages[index] = $scope.shopStages[index - 1];
            $scope.shopStages[index - 1] = $scope.stageSelected;
        }
    }


        // move down the selected stage in the list
    $scope.moveStageDown = function(stageCode){

        const index = $scope.shopStages.findIndex(stage => stage.code === stageCode);
        
        if (index == ($scope.shopStages.length - 1)){
            
            return;     // if there is only one stage do nothing

        } else {

            $scope.shopStages[index] = $scope.shopStages[index + 1];
            $scope.shopStages[index + 1] = $scope.stageSelected;
        }
    }


    $scope.Update_Stages = function()
    {
//        console.log(JSON.stringify($scope.shopStages));
        $http.post('../../php/Shop_Stage_Headings.php?shopID=' + $scope.selectedShop.id, JSON.stringify($scope.shopStages))
            .then(function(response) {
                console.log("Shop stages updated successfully!");
                console.log(response.data);
            })
            .catch(function(error) {
                console.error("Error updating shop stages:", error);
            });
    }   // $scope.Update_Stages()

}   // defineShopStagesCtrlr()

app.controller("DefineShopStagesCtrlr", defineShopStagesCtrlr);