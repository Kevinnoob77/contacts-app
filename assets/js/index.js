
document.addEventListener('DOMContentLoaded', function (e) {
  const alert = document.querySelector('.alert.success');
  if (alert != null) {
    setTimeout(() => {
      alert.remove();
    }, 4000);
  }

  // const deleteCategory = document.querySelectorAll('#delete-btn');
  // const modal = document.querySelector('#modal');
  // const modalToggle = document.querySelector('.modal-toggle');

  // deleteCategory.forEach(btn => {
  //   if (!btn.classList.contains('inactive')) {
  //     btn.addEventListener('click', () => {
  //       modalToggle.checked = true;
  //     });
  //   }
  // });

  // modal.addEventListener('click', (e) => {
  //   if (e.target === modal) {
  //     modalToggle.checked = false;;
  //   }
  // });

});
