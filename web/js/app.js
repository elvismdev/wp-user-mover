var app = angular.module('app', ['ngAnimate', 'ui.bootstrap']);

app.controller('WebCastCtrl', ['$scope', '$interval', 'StatsHeartbeatService', function ($scope, $interval, StatsHeartbeatService) {
    // Required Ajax variables
    $scope.groupID = 0;
    $scope.quantity = 100;
    $scope.offset = 0;

    // Ajax result queries
    $scope.execs = [];

    // Show current query stats
    $scope.stats = false;

    // Starts stats's heartbeat
    $scope.heartbeat = function () {
    	$scope.stats = StatsHeartbeatService.heartbeat();
    };
}]);

app.service('StatsHeartbeatService', ['$http', '$interval', function ($http, $interval) {
	this.promise = null;

    // Toggle heartbeat
    this.heartbeat = function () {
    	if (this.promise) {
    		$interval.cancel(this.promise);
    		this.promise = null;

    		return false;
    	} else {
    		this.promise = $interval(function () {
    			console.log('Stats Heartbear')
    		}, 3000);

    		return true;
    	}
    };
}]);