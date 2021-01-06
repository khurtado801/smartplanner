var app = angular.module('directiveApp', []);
/*app.directive('validPasswordC', function () {
    return {
        require: 'ngModel',
        scope: {
            reference: '=validPasswordC'

        },
        link: function (scope, elm, attrs, ctrl) {
            ctrl.$parsers.unshift(function (viewValue, $scope) {

                var noMatch = viewValue != scope.reference
                ctrl.$setValidity('noMatch', !noMatch);
                return (noMatch) ? noMatch : !noMatch;
            });

            scope.$watch("reference", function (value) {
                ;
                ctrl.$setValidity('noMatch', value === ctrl.$viewValue);

            });
        }
    }
});*/

app.directive('autoActive', ['$location', function ($location) {
    return {
        restrict: 'A',
        scope: false,
        link: function (scope, element, attrs) {
            function setActive() {
                var path = $location.path();
                if (path) {
                    angular.forEach(element.find('li'), function (li) {
                        var anchor = li.querySelector('a');
                        var datahref = anchor.attributes['data-href'].value;
                        if (datahref.match('#' + path + '(?=\\?|$)')) {
                            angular.element(li).addClass('active');
                        } else {
                            angular.element(li).removeClass('active');
                        }
                    });
                }
            }

            setActive();

            scope.$on('$locationChangeSuccess', setActive);
        }
    }
}]);

app.directive('yearDrop', function () {
    function getYears(offset, range) {
        var currentYear = new Date().getFullYear();
        var years = [];
        for (var i = 0; i < range + 1; i++) {
            years.push(currentYear + offset + i);
        }
        return years;
    }
    return {
        link: function (scope, element, attrs) {
            scope.years = getYears(+attrs.offset, +attrs.range);
            scope.selected = scope.years[0];
        },
        template: '<select class="form-control" name="expiry_year" ng-model="expiry_year" required ng-model="selected" ng-options="y for y in years"><option value="">Years</option></select>'
    }
});

app.directive('onlyDigits', function () {
    return {
        restrict: 'A',
        require: '?ngModel',
        link: function (scope, element, attrs, modelCtrl) {
            modelCtrl.$parsers.push(function (inputValue) {
                if (inputValue == undefined) return '';
                var transformedInput = inputValue.replace(/[^0-9]/g, '');
                if (transformedInput !== inputValue) {
                    modelCtrl.$setViewValue(transformedInput);
                    modelCtrl.$render();
                }
                return transformedInput;
            });
        }
    };
});

app.directive('validPasswordC', function () {
    return {
        require: 'ngModel',
        scope: {
            reference: '=validPasswordC'
        },
        link: function (scope, elm, attrs, ctrl) {
            ctrl.$parsers.unshift(function (viewValue, $scope) {
                //console.log(viewValue);
                var noMatch = viewValue != scope.reference;
                ctrl.$setValidity('noMatch', !noMatch);
                //console.log(noMatch);
                return (noMatch) ? noMatch : !noMatch;
            });
            scope.$watch("reference", function (value) {
                ctrl.$setValidity('noMatch', value === ctrl.$viewValue);
            });

        }
    };
});

app.directive('matcholdPassword', function () {
    return {
        require: 'ngModel',
        scope: {
            reference: '=matcholdPassword'
        },
        link: function (scope, elm, attrs, ctrl) {
            ctrl.$parsers.unshift(function (viewValue, $scope) {
                var match = false;
                if (viewValue != "") {
                    match = viewValue === scope.reference;

                    ctrl.$setValidity('matchconfi', !match);
                }
                console.log('before=' + match);
                var last = (match) ? match : !match;
                console.log(last);
                return match;
            });
            scope.$watch("reference", function (value) {
                ctrl.$setValidity('matchconfi', value === ctrl.$viewValue);
            });

        }
    };
});

app.directive('nksOnlyNumber', function () {
    return {
        restrict: 'EA',
        require: 'ngModel',
        link: function (scope, element, attrs, ngModel) {
            scope.$watch(attrs.ngModel, function (newValue, oldValue) {
                var spiltArray = String(newValue).split("");

                if (attrs.allowNegative == "false") {
                    if (spiltArray[0] == '-') {
                        newValue = newValue.replace("-", "");
                        ngModel.$setViewValue(newValue);
                        ngModel.$render();
                    }
                }

                if (attrs.allowDecimal == "false") {
                    newValue = parseInt(newValue);
                    ngModel.$setViewValue(newValue);
                    ngModel.$render();
                }

                if (attrs.allowDecimal != "false") {
                    if (attrs.decimalUpto) {
                        var n = String(newValue).split(".");
                        if (n[1]) {
                            var n2 = n[1].slice(0, attrs.decimalUpto);
                            newValue = [n[0], n2].join(".");
                            ngModel.$setViewValue(newValue);
                            ngModel.$render();
                        }
                    }
                }


                if (spiltArray.length === 0) return;
                if (spiltArray.length === 1 && (spiltArray[0] == '-' || spiltArray[0] === '.')) return;
                if (spiltArray.length === 2 && newValue === '-.') return;

                /*Check it is number or not.*/
                if (isNaN(newValue)) {
                    ngModel.$setViewValue(oldValue);
                    ngModel.$render();
                }
            });
        }
    };
});

app.directive('activeLink', ['$location', function (location) {
    return {
        restrict: 'A',
        link: function (scope, element, attrs, controller) {
            var clazz = attrs.activeLink;
            var path = attrs.href;
            path = path.substring(1);
            scope.location = location;
            scope.$watch('location.path()', function (newPath) {
                if (path === newPath) {
                    element.parent().addClass(clazz);
                } else {
                    element.parent().removeClass(clazz);
                }
            });
        }
    };
}]);
