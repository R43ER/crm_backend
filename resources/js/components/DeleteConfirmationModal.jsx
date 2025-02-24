import React from 'react';
import { Modal, Button } from 'react-bootstrap';

const DeleteConfirmationModal = ({ show, onClose, onConfirm, contact }) => {
  return (
    <Modal show={show} onHide={onClose}>
      <Modal.Header closeButton>
        <Modal.Title>Подтверждение удаления</Modal.Title>
      </Modal.Header>
      <Modal.Body>
        Вы действительно хотите удалить контакт <strong>{contact.first_name} {contact.last_name}</strong>?
      </Modal.Body>
      <Modal.Footer>
        <Button variant="secondary" onClick={onClose}>
          Отмена
        </Button>
        <Button variant="danger" onClick={onConfirm}>
          Удалить
        </Button>
      </Modal.Footer>
    </Modal>
  );
};

export default DeleteConfirmationModal;
