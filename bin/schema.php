<?php return (static function() {
    $class = new \ReflectionClass(\spitfire\storage\database\Schema::class);
    $object = $class->newInstanceWithoutConstructor();

    (function() {
        $this->name = 'testdb';
        $this->layouts = (static function() {
            $class = new \ReflectionClass(\spitfire\collection\TypedCollection::class);
            $object = $class->newInstanceWithoutConstructor();

            (function() {
                $this->type = 'spitfire\\storage\\database\\Layout';
            })->bindTo($object, \spitfire\collection\TypedCollection::class)();

            (function() {
                $this->items = [
                    '_tags' => (static function() {
                        $class = new \ReflectionClass(\spitfire\storage\database\Layout::class);
                        $object = $class->newInstanceWithoutConstructor();

                        (function() {
                            $this->tablename = '_tags';
                            $this->fields = (static function() {
                                $class = new \ReflectionClass(\spitfire\collection\TypedCollection::class);
                                $object = $class->newInstanceWithoutConstructor();

                                (function() {
                                    $this->type = 'spitfire\\storage\\database\\Field';
                                })->bindTo($object, \spitfire\collection\TypedCollection::class)();

                                (function() {
                                    $this->items = [
                                        '_id' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = '_id';
                                                $this->type = 'long:unsigned';
                                                $this->autoIncrements = true;
                                                $this->nullable = false;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'tag' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'tag';
                                                $this->type = 'string:255';
                                                $this->autoIncrements = false;
                                                $this->nullable = true;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'created' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'created';
                                                $this->type = 'int:unsigned';
                                                $this->autoIncrements = false;
                                                $this->nullable = false;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'updated' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'updated';
                                                $this->type = 'int:unsigned';
                                                $this->autoIncrements = false;
                                                $this->nullable = true;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })()
                                    ];
                                })->bindTo($object, \spitfire\collection\Collection::class)();

                                return $object;
                            })();
                            $this->indexes = (static function() {
                                $class = new \ReflectionClass(\spitfire\collection\TypedCollection::class);
                                $object = $class->newInstanceWithoutConstructor();

                                (function() {
                                    $this->type = 'spitfire\\storage\\database\\IndexInterface';
                                })->bindTo($object, \spitfire\collection\TypedCollection::class)();

                                (function() {
                                    $this->items = [
                                        (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Index::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = '_PRIMARY';
                                                $this->fields = (static function() {
                                                    $class = new \ReflectionClass(\spitfire\collection\Collection::class);
                                                    $object = $class->newInstanceWithoutConstructor();

                                                    (function() {
                                                        $this->items = [
                                                            (static function() {
                                                                $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                                                $object = $class->newInstanceWithoutConstructor();

                                                                (function() {
                                                                    $this->name = '_id';
                                                                    $this->type = 'long:unsigned';
                                                                    $this->autoIncrements = true;
                                                                    $this->nullable = false;
                                                                })->bindTo($object, \spitfire\storage\database\Field::class)();

                                                                return $object;
                                                            })()
                                                        ];
                                                    })->bindTo($object, \spitfire\collection\Collection::class)();

                                                    return $object;
                                                })();
                                                $this->unique = true;
                                                $this->primary = true;
                                            })->bindTo($object, \spitfire\storage\database\Index::class)();

                                            return $object;
                                        })()
                                    ];
                                })->bindTo($object, \spitfire\collection\Collection::class)();

                                return $object;
                            })();
                            $this->events = (static function() {
                                $class = new \ReflectionClass(\spitfire\event\EventDispatch::class);
                                $object = $class->newInstanceWithoutConstructor();

                                (function() {
                                    $this->parent = null;
                                    $this->hooks = [
                                        'spitfire\\storage\\database\\events\\RecordBeforeInsertEvent' => (static function() {
                                            $object = new \spitfire\event\HookDispatcher;

                                            (function() {
                                                $this->listeners = [
                                                    (static function() {
                                                        $class = new \ReflectionClass(\spitfire\storage\database\events\UpdateTimestampListener::class);
                                                        $object = $class->newInstanceWithoutConstructor();

                                                        (function() {
                                                            $this->field = 'created';
                                                        })->bindTo($object, \spitfire\storage\database\events\UpdateTimestampListener::class)();

                                                        return $object;
                                                    })()
                                                ];
                                            })->bindTo($object, \spitfire\event\HookDispatcher::class)();

                                            return $object;
                                        })(),
                                        'spitfire\\storage\\database\\events\\RecordBeforeUpdateEvent' => (static function() {
                                            $object = new \spitfire\event\HookDispatcher;

                                            (function() {
                                                $this->listeners = [
                                                    (static function() {
                                                        $class = new \ReflectionClass(\spitfire\storage\database\events\UpdateTimestampListener::class);
                                                        $object = $class->newInstanceWithoutConstructor();

                                                        (function() {
                                                            $this->field = 'updated';
                                                        })->bindTo($object, \spitfire\storage\database\events\UpdateTimestampListener::class)();

                                                        return $object;
                                                    })()
                                                ];
                                            })->bindTo($object, \spitfire\event\HookDispatcher::class)();

                                            return $object;
                                        })()
                                    ];
                                })->bindTo($object, \spitfire\event\EventDispatch::class)();

                                return $object;
                            })();
                        })->bindTo($object, \spitfire\storage\database\Layout::class)();

                        return $object;
                    })(),
                    'apps' => (static function() {
                        $class = new \ReflectionClass(\spitfire\storage\database\Layout::class);
                        $object = $class->newInstanceWithoutConstructor();

                        (function() {
                            $this->tablename = 'apps';
                            $this->fields = (static function() {
                                $class = new \ReflectionClass(\spitfire\collection\TypedCollection::class);
                                $object = $class->newInstanceWithoutConstructor();

                                (function() {
                                    $this->type = 'spitfire\\storage\\database\\Field';
                                })->bindTo($object, \spitfire\collection\TypedCollection::class)();

                                (function() {
                                    $this->items = [
                                        '_id' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = '_id';
                                                $this->type = 'long:unsigned';
                                                $this->autoIncrements = true;
                                                $this->nullable = false;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'secret' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'secret';
                                                $this->type = 'string:255';
                                                $this->autoIncrements = false;
                                                $this->nullable = false;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'publickey' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'publickey';
                                                $this->type = 'string:255';
                                                $this->autoIncrements = false;
                                                $this->nullable = true;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'created' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'created';
                                                $this->type = 'int:unsigned';
                                                $this->autoIncrements = false;
                                                $this->nullable = false;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'updated' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'updated';
                                                $this->type = 'int:unsigned';
                                                $this->autoIncrements = false;
                                                $this->nullable = true;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'removed' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'removed';
                                                $this->type = 'int:unsigned';
                                                $this->autoIncrements = false;
                                                $this->nullable = true;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })()
                                    ];
                                })->bindTo($object, \spitfire\collection\Collection::class)();

                                return $object;
                            })();
                            $this->indexes = (static function() {
                                $class = new \ReflectionClass(\spitfire\collection\TypedCollection::class);
                                $object = $class->newInstanceWithoutConstructor();

                                (function() {
                                    $this->type = 'spitfire\\storage\\database\\IndexInterface';
                                })->bindTo($object, \spitfire\collection\TypedCollection::class)();

                                (function() {
                                    $this->items = [
                                        (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Index::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = '_PRIMARY';
                                                $this->fields = (static function() {
                                                    $class = new \ReflectionClass(\spitfire\collection\Collection::class);
                                                    $object = $class->newInstanceWithoutConstructor();

                                                    (function() {
                                                        $this->items = [
                                                            (static function() {
                                                                $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                                                $object = $class->newInstanceWithoutConstructor();

                                                                (function() {
                                                                    $this->name = '_id';
                                                                    $this->type = 'long:unsigned';
                                                                    $this->autoIncrements = true;
                                                                    $this->nullable = false;
                                                                })->bindTo($object, \spitfire\storage\database\Field::class)();

                                                                return $object;
                                                            })()
                                                        ];
                                                    })->bindTo($object, \spitfire\collection\Collection::class)();

                                                    return $object;
                                                })();
                                                $this->unique = true;
                                                $this->primary = true;
                                            })->bindTo($object, \spitfire\storage\database\Index::class)();

                                            return $object;
                                        })()
                                    ];
                                })->bindTo($object, \spitfire\collection\Collection::class)();

                                return $object;
                            })();
                            $this->events = (static function() {
                                $class = new \ReflectionClass(\spitfire\event\EventDispatch::class);
                                $object = $class->newInstanceWithoutConstructor();

                                (function() {
                                    $this->parent = null;
                                    $this->hooks = [
                                        'spitfire\\storage\\database\\events\\RecordBeforeInsertEvent' => (static function() {
                                            $object = new \spitfire\event\HookDispatcher;

                                            (function() {
                                                $this->listeners = [
                                                    (static function() {
                                                        $class = new \ReflectionClass(\spitfire\storage\database\events\UpdateTimestampListener::class);
                                                        $object = $class->newInstanceWithoutConstructor();

                                                        (function() {
                                                            $this->field = 'created';
                                                        })->bindTo($object, \spitfire\storage\database\events\UpdateTimestampListener::class)();

                                                        return $object;
                                                    })()
                                                ];
                                            })->bindTo($object, \spitfire\event\HookDispatcher::class)();

                                            return $object;
                                        })(),
                                        'spitfire\\storage\\database\\events\\RecordBeforeUpdateEvent' => (static function() {
                                            $object = new \spitfire\event\HookDispatcher;

                                            (function() {
                                                $this->listeners = [
                                                    (static function() {
                                                        $class = new \ReflectionClass(\spitfire\storage\database\events\UpdateTimestampListener::class);
                                                        $object = $class->newInstanceWithoutConstructor();

                                                        (function() {
                                                            $this->field = 'updated';
                                                        })->bindTo($object, \spitfire\storage\database\events\UpdateTimestampListener::class)();

                                                        return $object;
                                                    })()
                                                ];
                                            })->bindTo($object, \spitfire\event\HookDispatcher::class)();

                                            return $object;
                                        })(),
                                        'spitfire\\storage\\database\\events\\RecordBeforeDeleteEvent' => (static function() {
                                            $object = new \spitfire\event\HookDispatcher;

                                            (function() {
                                                $this->listeners = [
                                                    (static function() {
                                                        $class = new \ReflectionClass(\spitfire\storage\database\events\SoftDeleteListener::class);
                                                        $object = $class->newInstanceWithoutConstructor();

                                                        (function() {
                                                            $this->field = 'removed';
                                                        })->bindTo($object, \spitfire\storage\database\events\SoftDeleteListener::class)();

                                                        return $object;
                                                    })()
                                                ];
                                            })->bindTo($object, \spitfire\event\HookDispatcher::class)();

                                            return $object;
                                        })()
                                    ];
                                })->bindTo($object, \spitfire\event\EventDispatch::class)();

                                return $object;
                            })();
                        })->bindTo($object, \spitfire\storage\database\Layout::class)();

                        return $object;
                    })(),
                    'files' => (static function() {
                        $class = new \ReflectionClass(\spitfire\storage\database\Layout::class);
                        $object = $class->newInstanceWithoutConstructor();

                        (function() {
                            $this->tablename = 'files';
                            $this->fields = (static function() {
                                $class = new \ReflectionClass(\spitfire\collection\TypedCollection::class);
                                $object = $class->newInstanceWithoutConstructor();

                                (function() {
                                    $this->type = 'spitfire\\storage\\database\\Field';
                                })->bindTo($object, \spitfire\collection\TypedCollection::class)();

                                (function() {
                                    $this->items = [
                                        '_id' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = '_id';
                                                $this->type = 'long:unsigned';
                                                $this->autoIncrements = true;
                                                $this->nullable = false;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'filename' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'filename';
                                                $this->type = 'string:255';
                                                $this->autoIncrements = false;
                                                $this->nullable = false;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'poster' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'poster';
                                                $this->type = 'string:255';
                                                $this->autoIncrements = false;
                                                $this->nullable = false;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'lqip' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'lqip';
                                                $this->type = 'string:1024';
                                                $this->autoIncrements = false;
                                                $this->nullable = false;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'contentType' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'contentType';
                                                $this->type = 'string:255';
                                                $this->autoIncrements = false;
                                                $this->nullable = false;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'animated' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'animated';
                                                $this->type = 'int:unsigned';
                                                $this->autoIncrements = false;
                                                $this->nullable = false;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'length' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'length';
                                                $this->type = 'int:unsigned';
                                                $this->autoIncrements = false;
                                                $this->nullable = false;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'md5' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'md5';
                                                $this->type = 'string:255';
                                                $this->autoIncrements = false;
                                                $this->nullable = false;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'created' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'created';
                                                $this->type = 'int:unsigned';
                                                $this->autoIncrements = false;
                                                $this->nullable = false;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'updated' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'updated';
                                                $this->type = 'int:unsigned';
                                                $this->autoIncrements = false;
                                                $this->nullable = true;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })()
                                    ];
                                })->bindTo($object, \spitfire\collection\Collection::class)();

                                return $object;
                            })();
                            $this->indexes = (static function() {
                                $class = new \ReflectionClass(\spitfire\collection\TypedCollection::class);
                                $object = $class->newInstanceWithoutConstructor();

                                (function() {
                                    $this->type = 'spitfire\\storage\\database\\IndexInterface';
                                })->bindTo($object, \spitfire\collection\TypedCollection::class)();

                                (function() {
                                    $this->items = [
                                        (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Index::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = '_PRIMARY';
                                                $this->fields = (static function() {
                                                    $class = new \ReflectionClass(\spitfire\collection\Collection::class);
                                                    $object = $class->newInstanceWithoutConstructor();

                                                    (function() {
                                                        $this->items = [
                                                            (static function() {
                                                                $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                                                $object = $class->newInstanceWithoutConstructor();

                                                                (function() {
                                                                    $this->name = '_id';
                                                                    $this->type = 'long:unsigned';
                                                                    $this->autoIncrements = true;
                                                                    $this->nullable = false;
                                                                })->bindTo($object, \spitfire\storage\database\Field::class)();

                                                                return $object;
                                                            })()
                                                        ];
                                                    })->bindTo($object, \spitfire\collection\Collection::class)();

                                                    return $object;
                                                })();
                                                $this->unique = true;
                                                $this->primary = true;
                                            })->bindTo($object, \spitfire\storage\database\Index::class)();

                                            return $object;
                                        })(),
                                        (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Index::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'hashidx';
                                                $this->fields = (static function() {
                                                    $class = new \ReflectionClass(\spitfire\collection\Collection::class);
                                                    $object = $class->newInstanceWithoutConstructor();

                                                    (function() {
                                                        $this->items = [
                                                            (static function() {
                                                                $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                                                $object = $class->newInstanceWithoutConstructor();

                                                                (function() {
                                                                    $this->name = 'md5';
                                                                    $this->type = 'string:255';
                                                                    $this->autoIncrements = false;
                                                                    $this->nullable = false;
                                                                })->bindTo($object, \spitfire\storage\database\Field::class)();

                                                                return $object;
                                                            })(),
                                                            (static function() {
                                                                $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                                                $object = $class->newInstanceWithoutConstructor();

                                                                (function() {
                                                                    $this->name = 'length';
                                                                    $this->type = 'int:unsigned';
                                                                    $this->autoIncrements = false;
                                                                    $this->nullable = false;
                                                                })->bindTo($object, \spitfire\storage\database\Field::class)();

                                                                return $object;
                                                            })()
                                                        ];
                                                    })->bindTo($object, \spitfire\collection\Collection::class)();

                                                    return $object;
                                                })();
                                                $this->unique = false;
                                                $this->primary = false;
                                            })->bindTo($object, \spitfire\storage\database\Index::class)();

                                            return $object;
                                        })()
                                    ];
                                })->bindTo($object, \spitfire\collection\Collection::class)();

                                return $object;
                            })();
                            $this->events = (static function() {
                                $class = new \ReflectionClass(\spitfire\event\EventDispatch::class);
                                $object = $class->newInstanceWithoutConstructor();

                                (function() {
                                    $this->parent = null;
                                    $this->hooks = [
                                        'spitfire\\storage\\database\\events\\RecordBeforeInsertEvent' => (static function() {
                                            $object = new \spitfire\event\HookDispatcher;

                                            (function() {
                                                $this->listeners = [
                                                    (static function() {
                                                        $class = new \ReflectionClass(\spitfire\storage\database\events\UpdateTimestampListener::class);
                                                        $object = $class->newInstanceWithoutConstructor();

                                                        (function() {
                                                            $this->field = 'created';
                                                        })->bindTo($object, \spitfire\storage\database\events\UpdateTimestampListener::class)();

                                                        return $object;
                                                    })()
                                                ];
                                            })->bindTo($object, \spitfire\event\HookDispatcher::class)();

                                            return $object;
                                        })(),
                                        'spitfire\\storage\\database\\events\\RecordBeforeUpdateEvent' => (static function() {
                                            $object = new \spitfire\event\HookDispatcher;

                                            (function() {
                                                $this->listeners = [
                                                    (static function() {
                                                        $class = new \ReflectionClass(\spitfire\storage\database\events\UpdateTimestampListener::class);
                                                        $object = $class->newInstanceWithoutConstructor();

                                                        (function() {
                                                            $this->field = 'updated';
                                                        })->bindTo($object, \spitfire\storage\database\events\UpdateTimestampListener::class)();

                                                        return $object;
                                                    })()
                                                ];
                                            })->bindTo($object, \spitfire\event\HookDispatcher::class)();

                                            return $object;
                                        })()
                                    ];
                                })->bindTo($object, \spitfire\event\EventDispatch::class)();

                                return $object;
                            })();
                        })->bindTo($object, \spitfire\storage\database\Layout::class)();

                        return $object;
                    })(),
                    'uploads' => (static function() {
                        $class = new \ReflectionClass(\spitfire\storage\database\Layout::class);
                        $object = $class->newInstanceWithoutConstructor();

                        (function() {
                            $this->tablename = 'uploads';
                            $this->fields = (static function() {
                                $class = new \ReflectionClass(\spitfire\collection\TypedCollection::class);
                                $object = $class->newInstanceWithoutConstructor();

                                (function() {
                                    $this->type = 'spitfire\\storage\\database\\Field';
                                })->bindTo($object, \spitfire\collection\TypedCollection::class)();

                                (function() {
                                    $this->items = [
                                        '_id' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = '_id';
                                                $this->type = 'long:unsigned';
                                                $this->autoIncrements = true;
                                                $this->nullable = false;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'file' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'file';
                                                $this->type = 'long:unsigned';
                                                $this->autoIncrements = false;
                                                $this->nullable = true;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'app' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'app';
                                                $this->type = 'long:unsigned';
                                                $this->autoIncrements = false;
                                                $this->nullable = true;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'secret' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'secret';
                                                $this->type = 'string:255';
                                                $this->autoIncrements = false;
                                                $this->nullable = false;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'created' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'created';
                                                $this->type = 'int:unsigned';
                                                $this->autoIncrements = false;
                                                $this->nullable = false;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'updated' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'updated';
                                                $this->type = 'int:unsigned';
                                                $this->autoIncrements = false;
                                                $this->nullable = true;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })(),
                                        'removed' => (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'removed';
                                                $this->type = 'int:unsigned';
                                                $this->autoIncrements = false;
                                                $this->nullable = true;
                                            })->bindTo($object, \spitfire\storage\database\Field::class)();

                                            return $object;
                                        })()
                                    ];
                                })->bindTo($object, \spitfire\collection\Collection::class)();

                                return $object;
                            })();
                            $this->indexes = (static function() {
                                $class = new \ReflectionClass(\spitfire\collection\TypedCollection::class);
                                $object = $class->newInstanceWithoutConstructor();

                                (function() {
                                    $this->type = 'spitfire\\storage\\database\\IndexInterface';
                                })->bindTo($object, \spitfire\collection\TypedCollection::class)();

                                (function() {
                                    $this->items = [
                                        (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\Index::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = '_PRIMARY';
                                                $this->fields = (static function() {
                                                    $class = new \ReflectionClass(\spitfire\collection\Collection::class);
                                                    $object = $class->newInstanceWithoutConstructor();

                                                    (function() {
                                                        $this->items = [
                                                            (static function() {
                                                                $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                                                $object = $class->newInstanceWithoutConstructor();

                                                                (function() {
                                                                    $this->name = '_id';
                                                                    $this->type = 'long:unsigned';
                                                                    $this->autoIncrements = true;
                                                                    $this->nullable = false;
                                                                })->bindTo($object, \spitfire\storage\database\Field::class)();

                                                                return $object;
                                                            })()
                                                        ];
                                                    })->bindTo($object, \spitfire\collection\Collection::class)();

                                                    return $object;
                                                })();
                                                $this->unique = true;
                                                $this->primary = true;
                                            })->bindTo($object, \spitfire\storage\database\Index::class)();

                                            return $object;
                                        })(),
                                        (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\ForeignKey::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'fk_file';
                                                $this->field = (static function() {
                                                    $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                                    $object = $class->newInstanceWithoutConstructor();

                                                    (function() {
                                                        $this->name = 'file';
                                                        $this->type = 'long:unsigned';
                                                        $this->autoIncrements = false;
                                                        $this->nullable = true;
                                                    })->bindTo($object, \spitfire\storage\database\Field::class)();

                                                    return $object;
                                                })();
                                                $this->referenced = (static function() {
                                                    $class = new \ReflectionClass(\spitfire\storage\database\identifiers\FieldIdentifier::class);
                                                    $object = $class->newInstanceWithoutConstructor();

                                                    (function() {
                                                        $this->raw = [
                                                            'files',
                                                            '_id'
                                                        ];
                                                    })->bindTo($object, \spitfire\storage\database\identifiers\FieldIdentifier::class)();

                                                    return $object;
                                                })();
                                            })->bindTo($object, \spitfire\storage\database\ForeignKey::class)();

                                            return $object;
                                        })(),
                                        (static function() {
                                            $class = new \ReflectionClass(\spitfire\storage\database\ForeignKey::class);
                                            $object = $class->newInstanceWithoutConstructor();

                                            (function() {
                                                $this->name = 'fk_app';
                                                $this->field = (static function() {
                                                    $class = new \ReflectionClass(\spitfire\storage\database\Field::class);
                                                    $object = $class->newInstanceWithoutConstructor();

                                                    (function() {
                                                        $this->name = 'app';
                                                        $this->type = 'long:unsigned';
                                                        $this->autoIncrements = false;
                                                        $this->nullable = true;
                                                    })->bindTo($object, \spitfire\storage\database\Field::class)();

                                                    return $object;
                                                })();
                                                $this->referenced = (static function() {
                                                    $class = new \ReflectionClass(\spitfire\storage\database\identifiers\FieldIdentifier::class);
                                                    $object = $class->newInstanceWithoutConstructor();

                                                    (function() {
                                                        $this->raw = [
                                                            'apps',
                                                            '_id'
                                                        ];
                                                    })->bindTo($object, \spitfire\storage\database\identifiers\FieldIdentifier::class)();

                                                    return $object;
                                                })();
                                            })->bindTo($object, \spitfire\storage\database\ForeignKey::class)();

                                            return $object;
                                        })()
                                    ];
                                })->bindTo($object, \spitfire\collection\Collection::class)();

                                return $object;
                            })();
                            $this->events = (static function() {
                                $class = new \ReflectionClass(\spitfire\event\EventDispatch::class);
                                $object = $class->newInstanceWithoutConstructor();

                                (function() {
                                    $this->parent = null;
                                    $this->hooks = [
                                        'spitfire\\storage\\database\\events\\RecordBeforeInsertEvent' => (static function() {
                                            $object = new \spitfire\event\HookDispatcher;

                                            (function() {
                                                $this->listeners = [
                                                    (static function() {
                                                        $class = new \ReflectionClass(\spitfire\storage\database\events\UpdateTimestampListener::class);
                                                        $object = $class->newInstanceWithoutConstructor();

                                                        (function() {
                                                            $this->field = 'created';
                                                        })->bindTo($object, \spitfire\storage\database\events\UpdateTimestampListener::class)();

                                                        return $object;
                                                    })()
                                                ];
                                            })->bindTo($object, \spitfire\event\HookDispatcher::class)();

                                            return $object;
                                        })(),
                                        'spitfire\\storage\\database\\events\\RecordBeforeUpdateEvent' => (static function() {
                                            $object = new \spitfire\event\HookDispatcher;

                                            (function() {
                                                $this->listeners = [
                                                    (static function() {
                                                        $class = new \ReflectionClass(\spitfire\storage\database\events\UpdateTimestampListener::class);
                                                        $object = $class->newInstanceWithoutConstructor();

                                                        (function() {
                                                            $this->field = 'updated';
                                                        })->bindTo($object, \spitfire\storage\database\events\UpdateTimestampListener::class)();

                                                        return $object;
                                                    })()
                                                ];
                                            })->bindTo($object, \spitfire\event\HookDispatcher::class)();

                                            return $object;
                                        })(),
                                        'spitfire\\storage\\database\\events\\RecordBeforeDeleteEvent' => (static function() {
                                            $object = new \spitfire\event\HookDispatcher;

                                            (function() {
                                                $this->listeners = [
                                                    (static function() {
                                                        $class = new \ReflectionClass(\spitfire\storage\database\events\SoftDeleteListener::class);
                                                        $object = $class->newInstanceWithoutConstructor();

                                                        (function() {
                                                            $this->field = 'removed';
                                                        })->bindTo($object, \spitfire\storage\database\events\SoftDeleteListener::class)();

                                                        return $object;
                                                    })()
                                                ];
                                            })->bindTo($object, \spitfire\event\HookDispatcher::class)();

                                            return $object;
                                        })()
                                    ];
                                })->bindTo($object, \spitfire\event\EventDispatch::class)();

                                return $object;
                            })();
                        })->bindTo($object, \spitfire\storage\database\Layout::class)();

                        return $object;
                    })()
                ];
            })->bindTo($object, \spitfire\collection\Collection::class)();

            return $object;
        })();
    })->bindTo($object, \spitfire\storage\database\Schema::class)();

    return $object;
})();