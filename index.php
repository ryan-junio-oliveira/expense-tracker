<?php

const DATA_FILE = 'expenses.json';

function loadExpenses() {
    if (!file_exists(DATA_FILE)) {
        return [];
    }
    $data = file_get_contents(DATA_FILE);
    return json_decode($data, true) ?: [];
}

function saveExpenses($expenses) {
    file_put_contents(DATA_FILE, json_encode($expenses, JSON_PRETTY_PRINT));
}

function addExpense($description, $amount) {
    $expenses = loadExpenses();
    $id = count($expenses) + 1;
    $expenses[] = [
        'id' => $id,
        'date' => date('Y-m-d'),
        'description' => $description,
        'amount' => $amount
    ];
    saveExpenses($expenses);
    echo "Expense added successfully (ID: $id)\n";
}

function listExpenses() {
    $expenses = loadExpenses();
    echo "ID  Date       Description  Amount\n";
    foreach ($expenses as $expense) {
        echo "{$expense['id']}   {$expense['date']}  {$expense['description']}  \${$expense['amount']}\n";
    }
}

function deleteExpense($id) {
    $expenses = loadExpenses();
    foreach ($expenses as $index => $expense) {
        if ($expense['id'] == $id) {
            unset($expenses[$index]);
            saveExpenses(array_values($expenses));
            echo "Expense deleted successfully\n";
            return;
        }
    }
    echo "Error: Expense ID not found\n";
}

function summary($month = null) {
    $expenses = loadExpenses();
    $total = 0;
    foreach ($expenses as $expense) {
        if ($month === null || date('n', strtotime($expense['date'])) == $month) {
            $total += $expense['amount'];
        }
    }
    if ($month) {
        echo "Total expenses for month $month: \$$total\n";
    } else {
        echo "Total expenses: \$$total\n";
    }
}

echo "Selecione dentre os comandos a seguir: \n\n";
echo "add\nlist\ndelete\nsummary\n:";
$command = trim(fgets(STDIN));

switch ($command) {
    case 'add':
        echo "Descrição da despesa: ";
        $description = trim(fgets(STDIN));
        echo "Valor da despesa: ";
        $amount = (float)trim(fgets(STDIN));
        if ($description && $amount > 0) {
            addExpense($description, $amount);
        } else {
            echo "Error: Argumentos inválidos para adicionar despesa.\n";
        }
        break;
    
    case 'list':
        listExpenses();
        break;

    case 'delete':
        echo "ID da despesa para deletar: ";
        $id = (int)trim(fgets(STDIN));
        if ($id > 0) {
            deleteExpense($id);
        } else {
            echo "Error: ID de despesa inválido.\n";
        }
        break;

    case 'summary':
        echo "Informe o mês para resumo (1-12, ou pressione Enter para total): ";
        $monthInput = trim(fgets(STDIN));
        $month = ($monthInput !== '') ? (int)$monthInput : null;
        summary($month > 0 && $month <= 12 ? $month : null);
        break;

    default:
        echo "Uso: expense-tracker <comando>\n";
        echo "Comandos: add, list, delete, summary\n";
        break;
}
