/*
 * Copyright (c) 2018. Rolfe Bozier
 */

$('#imageDisplay').on('show.bs.modal', function(e) {
  var m_photo = e.relatedTarget.dataset.photo;
  var m_location = e.relatedTarget.dataset.location;
  var m_text = e.relatedTarget.dataset.text;
  var m_uid = e.relatedTarget.dataset.uid;
  var m_fullname = e.relatedTarget.dataset.fullname;
  document.getElementById('modal-location').innerHTML = m_location;
  document.getElementById('modal-photo').src = m_photo;
  document.getElementById('modal-text').innerHTML = m_text;
  document.getElementById('modal-owner').innerHTML = m_fullname;
  document.getElementById('modal-contact').href = '/c/lib/mailer.php?uid=' + m_uid;
});
