/**/
var ResponsiveSlider;

ResponsiveSlider = function() {
	function a(a) {
		var b, c, d;
		d = this;
		for (b in a) c = a[b], d[b], d[b] = c;
		d.nextButton && d.nextButton.addEventListener("click", function(a) {
			return function() {
				return a.clickHandler("next")
			}
		}(this)), d.previousButton && d.previousButton.addEventListener(
			"click",
			function(a) {
				return function() {
					return a.clickHandler("previous")
				}
			}(this)), d.element.addEventListener("mouseenter", function(
			a) {
			return function() {
				return a.action("stop")
			}
		}(this)), d.element.addEventListener("mouseleave", function(a) {
			return function() {
				return a.action("start")
			}
		}(this))
	}
	return a.prototype.element = "defaultValue", a.prototype.radios =
		"defaultValue", a.prototype.currentIndex = 0, a.prototype
		.nIntervalId = "defaultValue", a.prototype.nextButton = null, a
		.prototype.previousButton = null, a.prototype.interval = 5e3, a
		.prototype.action = function(a) {
			var b;
			switch (b = this, a) {
				case "start":
					b.nIntervalId = window.setInterval(function() {
						return b.gotoItem()
					}, b.interval);
					break;
				default:
					return clearInterval(b.nIntervalId)
			}
		}, a.prototype.gotoItem = function(a, b) {
			var c;
			return c = this, b || (b = "next"), 0 !== a && (a || (a = c
				.getNextIndex(b))), (-1 === a || a === c.radios.length +
				1) && (a = 0), c.radios[a].checked = !0
		}, a.prototype.getNextIndex = function(a) {
			var b, c;
			switch (c = this, a) {
				case "previous":
					b = c.currentIndex - 1, -1 === b && (b = c.radios
						.length - 1);
					break;
				default:
					b = c.currentIndex + 1, b === c.radios.length && (b = 0)
			}
			return c.currentIndex = b, b
		}, a.prototype.clickHandler = function(a) {
			var b;
			return b = this, a || (a = "next"), b.action("stop"), b
				.gotoItem(b.getNextIndex(a), a)
		}, a
}();
/**/
