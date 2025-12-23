document.addEventListener("DOMContentLoaded", function() {
const div = document.querySelector("#footer");

if (div) {
  div.outerHTML = `
                <div class="row bg-color p-4 text-white">
                    <div class="col col-12 col-xl-4">
                        
                        
                
                    <!-- Copyright -->
                    <div class="text-center text-14">
                        <span>
                            <a class="text-main text-decoration-none">FUN TEAM</a>
                            </span>
                    </div>
                </div>
            `;
}
});