;(function ($) {
	$(document).on(
		'click',
		'.woo-faq-container .accordion .accordion-item > button, .woo-faq-archive-container .accordion .accordion-item > button',
		function () {
			var $btn = $(this);
			var isExpanded = $btn.attr('aria-expanded') === 'true';
			var $scope = $btn.closest('.woo-faq-container, .woo-faq-archive-container');

			if ($scope.length) {
				$scope.find('.accordion .accordion-item > button').attr('aria-expanded', 'false');
			}

			if (!isExpanded) {
				$btn.attr('aria-expanded', 'true');
			}
		}
	);
})(jQuery);
