'use strict';

travelManagerApp.service('Session', function () {
	  this.create = function (sessionId, userId, userRole, userType) {
		this.id = sessionId;
		this.userId = userId;
		this.userRole = userRole;
		this.userType = userType;
	  };
	  this.destroy = function () {
		this.id = null;
		this.userId = null;
		this.userRole = null;
		this.userType = null;
	  };
	  return this;
});
