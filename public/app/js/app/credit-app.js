'use strict';

var travelManagerApp = angular.module('travelManagerApp', ['ngRoute','ui.bootstrap'], function($locationProvider) {
      $locationProvider.html5Mode(true);
    });
