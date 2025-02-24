// src/components/EditableCell.jsx
import React, { useState, useEffect, useRef } from 'react';

const EditableCell = ({ value, onSave }) => {
  const [editing, setEditing] = useState(false);
  const [currentValue, setCurrentValue] = useState(value);
  const [hovered, setHovered] = useState(false);
  const inputRef = useRef(null);

  useEffect(() => {
    setCurrentValue(value);
  }, [value]);

  const handleSave = () => {
    setEditing(false);
    if (currentValue !== value) {
      onSave(currentValue);
    }
    // Сброс hovered с небольшой задержкой, чтобы иконка исчезла
    setTimeout(() => setHovered(false), 100);
  };

  const handleIconClick = (e) => {
    e.stopPropagation();
    setEditing(true);
  };

  const handleKeyDown = (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      if (inputRef.current) {
        inputRef.current.blur();
      }
      handleSave();
    }
  };

  if (editing) {
    return (
      <input
        ref={inputRef}
        type="text"
        value={currentValue}
        onChange={(e) => setCurrentValue(e.target.value)}
        onBlur={handleSave}
        onKeyDown={handleKeyDown}
        autoFocus
        style={{ width: '100%' }}
      />
    );
  }

  return (
    <div
      style={{
        position: 'relative',
        minHeight: '1.5em',
        paddingRight: '20px', // резервное пространство для иконки
      }}
      onMouseEnter={() => setHovered(true)}
      onMouseLeave={() => setHovered(false)}
    >
      <span>{value}</span>
      {hovered && (
        <span
          onClick={handleIconClick}
          style={{
            position: 'absolute',
            right: '0',
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

export default EditableCell;
