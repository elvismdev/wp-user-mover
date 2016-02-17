var app = angular.module('app', []);

app.controller('WebCastCtrl', ['$scope', '$http', '$interval', '$location', 'heartbeatService', function ($scope, $http, $interval, $location, heartbeatService) {
	// URL
	$scope.url = $location.absUrl();

	// Remove the backslash from the end of the URL string if this exists.
	if ( $scope.url.indexOf("/", $scope.url.length - 1 ) > -1 ) {
		$scope.url = $scope.url.slice(0, -1);
	}

	// Required Ajax variables
	$scope.groupID = 0;
	$scope.quantity = 100;
	$scope.total = 100;
	$scope.offset = 0;

	// Ajax result queries
	$scope.execs = [];

	// Show current query stats
	$scope.stats = null;

	// Progress
	$scope.progress_moved = 0;
	$scope.progress_exist = 0;
	$scope.progress_ignored = 0;
	$scope.progress_errors = 0;

	// Enable/Disable during ajax
	$scope.isAjax = false;

	// Init everything
	$scope.init = function () {
		$scope.offset = 0;

		// Progress
		$scope.progress_moved = 0;
		$scope.progress_exist = 0;
		$scope.progress_ignored = 0;
		$scope.progress_errors = 0;
	};

	// Execute move
	$scope.moveUsers = function (iteration) {
		$scope.isAjax = true;

		$http({
			method: 'POST',
			url: $scope.url + '/moveUsers/' + $scope.groupID + '/' + $scope.quantity,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'},
			transformRequest: function(obj) {
				var str = [];
				for(var p in obj)
					str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
				return str.join("&");
			},
			data: {offset: $scope.offset}
		}).then(function successCallback(response) {
			var exec = {
				moved: response.data.moved,
				exist: response.data.exist,
				ignored: response.data.ignored,
				errors: response.data.errors,
				offset: $scope.offset,
				quantity: $scope.quantity
			};
			$scope.execs.push(exec);

			iteration++;
			if (iteration < $scope.total / $scope.quantity) {
				$scope.offset = $scope.offset + $scope.quantity;
				$scope.moveUsers(iteration);
			} else {
				$scope.heartbeat();
				$scope.isAjax = false;
				$scope.init();
			}
		}, function errorCallback(response) {
			var exec = {
				moved: 0,
				exist: 0,
				ignored: 0,
				errors: 'Error ocurred during request',
				offset: $scope.offset,
				quantity: $scope.quantity
			};
			$scope.execs.push(exec);

			$scope.heartbeat();
			$scope.isAjax = false;
			$scope.init();
		});
	};

	$scope.heartbeat = function heartbeat () {
		if ($scope.stats) {
			$interval.cancel($scope.stats);
			$scope.stats = false;
		} else {
			$scope.stats = $interval(function () {
				heartbeatService.goGet($scope.url, $scope.quantity);

				$scope.progress_moved = heartbeatService.progress_moved;
				$scope.progress_exist = heartbeatService.progress_exist;
				$scope.progress_ignored = heartbeatService.progress_ignored;
				$scope.progress_errors = heartbeatService.progress_errors;
			}, 2000);
		}
	};
}]);

app.service('heartbeatService', ['$http', function ($http) {
	var self = this;

	this.progress_moved = 0;
	this.progress_exist = 0;
	this.progress_ignored = 0;
	this.progress_errors = 0;

	this.goGet = function (url, quantity) {
		$http({
			method: 'GET',
			url: url + '/heartbeatLog'
		}).then(function successCallback(response) {
			self.progress_moved = (parseInt(response.data.moved) * 100 / parseInt(quantity)).toFixed(0);
			self.progress_exist = (parseInt(response.data.exist) * 100 / parseInt(quantity)).toFixed(0);
			self.progress_ignored = (parseInt(response.data.ignored) * 100 / parseInt(quantity)).toFixed(0);
			self.progress_errors = (parseInt(response.data.errors * 100) / parseInt(quantity)).toFixed(0);
		}, function errorCallback(response) {
			this.progress_moved = 0;
			this.progress_exist = 0;
			this.progress_ignored = 0;
			this.progress_errors = 0;
		});
	};
}]);
