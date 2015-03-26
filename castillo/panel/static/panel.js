String.prototype.endsWith = function(suffix) {
    return this.indexOf(suffix, this.length - suffix.length) !== -1;
};

var castilloPanel = angular.module('panel', ['ui.bootstrap']);

castilloPanel.controller('PanelController', function($scope, $http) {

    var parentize = function(dir) {
        if (!_.has(dir, 'parent'))
        {
            dir.parent = null;
            dir.parents = [];
        }
        _.each(dir.children, function(child){
            child.parent = dir;
            child.parents = dir.parents.concat([dir]);
            if (child.type == 'directory')
                parentize(child);
        });
    }

    var connect = function(dir, blueprints) {
        var contentFile = _.find(dir.children, function(child){ return child.type == 'file' && child.name.endsWith('.txt')});
        
        if (contentFile) {
            dir.content = contentFile.content;
            dir.template = _.find(blueprints, function(bp){ return bp.name == contentFile.name; });
        }

        _.each(dir.children, function(child){
            if (child.type == 'directory')
                connect(child, blueprints);
        });
    }

    $http.get('/panel/api?action=list').success(function(data){
        $scope.content = data['content'];
        $scope.blueprints = data['blueprints'];

        parentize($scope.content);
        parentize($scope.blueprints);

        connect($scope.content, $scope.blueprints.children);

        $scope.currentPage = $scope.content;
    });

    $scope.switchPage = function(newPage) {
        $scope.currentPage = newPage;
    }

});


castilloPanel.directive('panelField', function(RecursionHelper) {
  return {
    replace: true,
    scope: {
      field: '=field'
    },
    templateUrl: 'panelfield.html',
    compile: function(element) {
        var linker = RecursionHelper.compile(element);

        return {
            pre: linker.pre,
            post: function(scope, element, attrs) {
                scope.opened = false;
                scope.open = function($event) {
                    $event.preventDefault();
                    $event.stopPropagation();

                    scope.opened = true;
                  };
                linker.post(scope, element, attrs);
            }
        };
    }
  };
});


castilloPanel.factory('RecursionHelper', ['$compile', function($compile){
    return {
        /**
         * Manually compiles the element, fixing the recursion loop.
         * @param element
         * @param [link] A post-link function, or an object with function(s) registered via pre and post properties.
         * @returns An object containing the linking functions.
         */
        compile: function(element, link){
            // Normalize the link parameter
            if(angular.isFunction(link)){
                link = { post: link };
            }

            // Break the recursion loop by removing the contents
            var contents = element.contents().remove();
            var compiledContents;
            return {
                pre: (link && link.pre) ? link.pre : null,
                /**
                 * Compiles and re-adds the contents
                 */
                post: function(scope, element){
                    // Compile the contents
                    if(!compiledContents){
                        compiledContents = $compile(contents);
                    }
                    // Re-add the compiled contents to the element
                    compiledContents(scope, function(clone){
                        element.append(clone);
                    });

                    // Call the post-linking function, if any
                    if(link && link.post){
                        link.post.apply(null, arguments);
                    }
                }
            };
        }
    };
}]);