#!/usr/bin/env python2

from __future__ import print_function
import sys
import argparse
import mysql.connector
from collections import namedtuple
import json

role_flags = {
    'editor':       0x01,
    'moderator':    0x02,
    'superuser':    0x04,
};

status_flags = {
    'unconfirmed':  0x01,
    'suspended':    0x02,
    'expired':      0x04,
    'locked':       0x08,
};

def parse_role_flag(str):
    return role_flags.get(str, None)

def parse_status_flag(str):
    return status_flags.get(str, None)

def list_users(db, args):
    c = db.cursor()
    role = 0
    if args.editor:
        role |= role_flags['editor']
    if args.moderator:
        role |= role_flags['moderator']
    if args.superuser:
        role |= role_flags['superuser']
    status = 0
    if args.unconfirmed:
        status |= status_flags['unconfirmed']
    if args.suspended:
        status |= status_flags['suspended']
    if args.expired:
        status |= status_flags['expired']
    if args.locked:
        status |= status_flags['locked']

    sql = "select * from r_user"
    where_str = []
    where_arg = []

    if role != 0:
        where_str.append("(role & %s) != 0")
        where_arg.append(role)
    if status != 0:
        where_str.append("(status & %s) != 0")
        where_arg.append(status)

    if len(where_str) > 0:
        sql += " where " + " and ".join(where_str)

    c.execute(sql, where_arg)

    print("{:>4s} {:30s} {:30s} {:4s} {:8s}".format("UID", "Username", "Email", "Role", "Status"))
    print("{:>4s} {:30s} {:30s} {:4s} {:8s}".format("---", "--------", "-----", "----", "------"))

    rowspec = namedtuple("row", c.column_names)
    while True:
        rawrow = c.fetchone()
        if rawrow:
            decoded_row = tuple([field.decode('utf-8') if type(field) is bytes else field for field in rawrow])
            row = rowspec(*decoded_row)

            role = ''
            if row.role != 0:
                if row.role & role_flags['editor']:
                    role += 'E'
                if row.role & role_flags['moderator']:
                    role += 'M'
                if row.role & role_flags['superuser']:
                    role += 'S'
            role = role or '-'

            status = ''
            if row.status != 0:
                if row.status & status_flags['unconfirmed']:
                    status += 'U'
                if row.status & status_flags['suspended']:
                    status += 'S'
                if row.status & status_flags['expired']:
                    status += 'E'
                if row.status & status_flags['locked']:
                    status += 'L'
            status = status or '-'
                
            print("{:4d} {:30s} {:30s} {:4s} {:8s}".format(
                row.uid,
                row.username,
                row.fullname,
                role,
                status
            ))
        else:
            break

    c.close()

def query_user(db, username, args):
    c = db.cursor()
    sql = "select * from r_user where username = %s"
    c.execute(sql, params=[username])

    rowspec = namedtuple("row", c.column_names)
    rawrow = c.fetchone()
    if rawrow:
        decoded_row = tuple([field.decode('utf-8') if type(field) is bytes else field for field in rawrow])
        row = rowspec(*decoded_row)

        print("{:16s}: {}".format("UID", row.uid))
        print("{:16s}: {}".format("username", row.username))
        print("{:16s}: {}".format("full name", row.fullname))
        print("{:16s}: {}".format("role", row.role))
        print("{:16s}: {}".format("status", row.status))
        print("{:16s}: {} ({})".format("last login", row.last_login_time, row.last_login_addr))
        print("{:16s}: {} ({})".format("register", row.register_time, row.register_addr))
    else:
        print("error: no such username: {}".format(username))

    c.close()

def update_user_status(db, username, vset, vclr):
    c = db.cursor()
    sql = "update r_user set status = (status & ~%s) | %s where username = %s"
    c.execute(sql, params=[vclr, vset, username])
    c.close()

def update_user_role(db, username, vset, vclr):
    c = db.cursor()
    sql = "update r_user set role = (role & ~%s) | %s where username = %s"
    c.execute(sql, params=[vclr, vset, username])
    c.close()

if __name__ == '__main__':
    p = argparse.ArgumentParser()
    p.add_argument('--config', type=str, metavar='FILE', required=True,
        help='Specify the path to the secure settings file')
    p.add_argument('--list', action='store_true',
        help='List all users')
    p.add_argument('--user', type=str, metavar='USERNAME',
        help='Query the given username')

    p.add_argument('--set_role', type=str, metavar='FLAG',
        help='Set the given role flag')
    p.add_argument('--clr_role', type=str, metavar='FLAG',
        help='Clear the given role flag')
    p.add_argument('--editor', action='store_true',
        help='Retrict listing to users with the editor role')
    p.add_argument('--moderator', action='store_true',
        help='Retrict listing to users with the moderator role')
    p.add_argument('--superuser', action='store_true',
        help='Retrict listing to users with the superuser role')

    p.add_argument('--set_status', type=str, metavar='FLAG',
        help='Set the given status flag')
    p.add_argument('--clr_status', type=str, metavar='FLAG',
        help='Clear the given status flag')
    p.add_argument('--unconfirmed', action='store_true',
        help='Retrict listing to unconfirmed users')
    p.add_argument('--suspended', action='store_true',
        help='Retrict listing to suspended users')
    p.add_argument('--expired', action='store_true',
        help='Retrict listing to expired users')
    p.add_argument('--locked', action='store_true',
        help='Retrict listing to locked users')

    args = p.parse_args()

    if args.config:
        with open(args.config) as fp:
            config = json.load(fp)
    else:
        print("error: no configuration file specified");
        sys.exit(1)

    db = mysql.connector.connect(user=config['db-user'], db=config['db-name'],
                                 password=config['db-password'], charset='utf8')

    if args.list:
        list_users(db, args)
    elif args.user:
        if args.set_role:
            v = parse_role_flag(args.set_role)
            if v is None:
                print("error: invalid role flag: " . args.set_role);
                sys.exit(1)
            update_user_role(db, args.user, v, 0)
        elif args.clr_role:
            v = parse_role_flag(args.clr_role)
            if v is None:
                print("error: invalid role flag: " . args.clr_role);
                sys.exit(1)
            update_user_role(db, args.user, 0, v)
        if args.set_status:
            v = parse_status_flag(args.set_status)
            if v is None:
                print("error: invalid status flag: " . args.set_status);
                sys.exit(1)
            update_user_status(db, args.user, v, 0)
        elif args.clr_status:
            v = parse_status_flag(args.clr_status)
            if v is None:
                print("error: invalid status flag: " . args.clr_status);
                sys.exit(1)
            update_user_status(db, args.user, 0, v)
        query_user(db, args.user, args)
    else:
        query_user(db, args.user, args)
