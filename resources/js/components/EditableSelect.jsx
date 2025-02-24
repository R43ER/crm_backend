// src/components/EditableSelect.jsx
import React, { useState, useEffect } from 'react';
import Select from 'react-select';

const EditableSelect = ({ value, options, onSave }) => {
  const [editing, setEditing] = useState(false);
  const [selectedOption, setSelectedOption] = useState(null);
  const [hovered, setHovered] = useState(false);

  useEffect(() => {
    const opt = options.find(option => option.value === value) || null;
    setSelectedOption(opt);
  }, [value, options]);

  const handleSelectChange = (option) => {
    setSelectedOption(option);
  };

  const handleSave = () => {
    setEditing(false);
    setHovered(false); // Сброс состояния наведения, чтобы иконка пропала
    if (selectedOption && selectedOption.value !== value) {
      onSave(selectedOption.value);
    }
  };

  if (editing) {
    return (
      <div
        onBlur={handleSave}
        style={{ minWidth: '150px' }}
      >
        <Select
          autoFocus
          options={options}
          value={selectedOption}
          onChange={handleSelectChange}
          onBlur={handleSave}
          menuPortalTarget={document.body}
          styles={{ menuPortal: base => ({ ...base, zIndex: 9999 }) }}
        />
      </div>
    );
  }

  return (
    <div
      style={{ position: 'relative', minWidth: '150px' }}
      onMouseEnter={() => setHovered(true)}
      onMouseLeave={() => setHovered(false)}
    >
      <span>{selectedOption ? selectedOption.label : ''}</span>
      {hovered && (
        <span
          onClick={() => setEditing(true)}
          style={{
            position: 'absolute',
            right: 0,
            top: '50%',
            transform: 'translateY(-50%)',
            cursor: 'pointer',
            color: '#007bff',
            userSelect: 'none',
          }}
          title="Редактировать"
        >
          ✎
        </span>
      )}
    </div>
  );
};

export default EditableSelect;
