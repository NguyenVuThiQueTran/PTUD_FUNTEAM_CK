import "../lib/jquery-3.6.4/jquery-3.6.4.js";
import "../lib/bootstrap-5.3.0-alpha1-dist/js/bootstrap.bundle.js"; // Bootstrap js
import "./header.js"; // Header render + cover layer
import "./footer.js"; // Footer render


// Items
function activeItem() {
	const items = document.querySelectorAll(".product-box");
	if (items) {
		items.forEach((item) => {
			item.addEventListener("click", (e) => {
				let local = new ItemViewStored();
				let id = e.target.parentElement.dataset.id;
				if (id) {
					local.itemID = Number.parseInt(id);
					local.saveToLocalStorage();
				} else {
					e.preventDefault();
				}
			});
		});
	}
}

function resetFilter() {
	const localFilter = new FilterStored();
	localFilter.sort = 0;
	localFilter.saveToLocalStorage();
}

function activeSubCategory(renderFunction) {
	const list = document.querySelectorAll("*[data-subcategory]");

	list.forEach((item) => {
		item.addEventListener("click", async (e) => {
			if (renderFunction) e.preventDefault();
			const itemSubcategoryID = Number(item.dataset.subcategory);
			const currentSubcategory = await getSubcategory(itemSubcategoryID);

			// subcategory
			const local = new SubCategoryViewStored();
			local.subcategoryID = itemSubcategoryID;
			local.saveToLocalStorage();

			// category
			const localCategory = new CategoryViewStored();
			localCategory.categoryID = Number(currentSubcategory.category_id);
			localCategory.saveToLocalStorage();

			resetFilter();
			renderFunction();
		});
	});
}

function activeCategory() {
	const list = document.querySelectorAll("*[data-category]");

	list.forEach((item) => {
		item.addEventListener("click", (e) => {
			const local = new CategoryViewStored();
			local.categoryID = Number(item.dataset.category);
			local.saveToLocalStorage();

			const subcategoryLocal = new SubCategoryViewStored();
			subcategoryLocal.subcategoryID = 0;
			subcategoryLocal.saveToLocalStorage();

			resetFilter();
		});
	});
}

// Horizontal Slider btn
/**
 * How to use
 * <section class="h-slider">
 * 	<ul class="h-slider__list">
 * 		<li class="h-slider__item"></li>
 * 	</ul>
 * </section>
 */
function activeHorizontalSlider() { 
	const hSliderList = document.querySelectorAll(".h-slider");
	Array.from(hSliderList).forEach((item) => {
		const sliderList = item.querySelector(".h-slider__list");

		// Clean btn when re-run
		try {
			item.querySelector(".h-slider__btn").remove();
			item.querySelector(".h-slider__btn").remove();2
		} catch (e) {}

		if (sliderList.scrollWidth >= sliderList.clientWidth) {
			let isSmall = sliderList.className.includes("--small");

			const bgColor = isSmall ? "black" : "var(--main-color)";
			const size = isSmall ? 20 : 30;
			const percentScroll = 35;
			const quantityScroll = Math.max(
				(percentScroll * sliderList.scrollWidth) / 100,
				1000
			);

			// Left button
			const btnLeft = document.createElement("button");
			btnLeft.style = `--width: ${size}px; --bg-color: ${bgColor}`;
			btnLeft.classList =
				"h-slider__btn left d-none d-lg-flex justify-content-center align-items-center";
			btnLeft.innerHTML = `<i class="fa-solid fa-angle-left"></i>`;

			// Right button
			const btnRight = document.createElement("button");
			btnRight.style = `--width: ${size}px; --bg-color: ${bgColor}`;
			btnRight.classList =
				"h-slider__btn right d-none d-lg-flex justify-content-center align-items-center";
			btnRight.innerHTML = `<i class="fa-solid fa-angle-right"></i>`;

			// Scroll method
			const scrollX = (x) => {
				sliderList.scroll({
					top: 0,
					left: x,
					behavior: "smooth",
				});
				sliderList.style.pointerEvents = "none";
				setTimeout(
					() => (sliderList.style.pointerEvents = "auto"),
					800
				);

				const scrollWidth = sliderList.scrollWidth;
				btnLeft.classList.toggle("d-lg-none", x <= 0);
				const isEnd =
					(x * 2) / scrollWidth > 0.8 ||
					scrollWidth - (x + sliderList.clientWidth) < 100;

				btnRight.classList.toggle("d-lg-none", isEnd);
			};

			// Reset
			scrollX(0);

			btnLeft.addEventListener("click", () =>
				scrollX(sliderList.scrollLeft - quantityScroll)
			);

			btnRight.addEventListener("click", () =>
				scrollX(sliderList.scrollLeft + quantityScroll)
			);

			item.appendChild(btnLeft);
			item.appendChild(btnRight);
		}
	});
}

activeHorizontalSlider();
export {
	activeHorizontalSlider,
	activeItem,
	activeSubCategory,
	activeCategory,
};
